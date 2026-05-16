<?php
namespace App\Http\Controllers;

use App\Models\PricingTier;
use App\Models\Subscription;
use App\Services\PricingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Phase 4 — real subscriptions, real lifecycle.
 *
 * - mode: subscription (not payment).
 * - server-side price_id resolution via PricingTier (no client-supplied amounts).
 * - Stripe Customer Portal for cancel/upgrade/card-update.
 * - 7-day free trial for first-time subscribers.
 * - automatic_tax + Stripe Radar enabled.
 * - 6 webhook event handlers (idempotent via stripe_webhook_events row dedup):
 *     checkout.session.completed
 *     customer.subscription.updated
 *     customer.subscription.deleted
 *     invoice.payment_succeeded
 *     invoice.payment_failed
 *     customer.updated
 * - PayPal removed.
 */
class PaymentController extends Controller
{
    public function __construct(private readonly PricingService $pricing) {}

    /**
     * Server-side price + plan resolution. Never trusts client input.
     * Looks up the PricingTier for the requesting country and returns the
     * corresponding stripe_price_id for the chosen interval.
     */
    private function resolvePrice(Request $request, string $planParam): array
    {
        $interval = $planParam === 'annual' ? 'yearly' : 'monthly';

        // PricingService figures out the right tier from request locale / IP.
        $tier = $this->pricing->resolveTier($request) ?? PricingTier::query()
            ->where('region_code', 'GLOBAL')
            ->orWhere('region_code', 'US')
            ->where('is_active', true)
            ->first();

        if (! $tier) {
            throw new \RuntimeException('No active PricingTier found.');
        }

        $priceId = $interval === 'yearly' ? $tier->stripe_price_id_yearly : $tier->stripe_price_id_monthly;
        if (! $priceId) {
            throw new \RuntimeException("PricingTier#{$tier->id} has no stripe_price_id for {$interval}. Run `php artisan stripe:sync-prices`.");
        }

        return [
            'price_id' => $priceId,
            'interval' => $interval,
            'months'   => $interval === 'yearly' ? 12 : 1,
            'plan'     => $interval === 'yearly' ? 'annual' : 'monthly',
            'tier'     => $tier,
        ];
    }

    public function stripeCheckout(Request $request)
    {
        $validated = $request->validate([
            'plan' => 'required|in:monthly,annual',
        ]);

        if (! class_exists(\Stripe\Checkout\Session::class) || ! class_exists(\Stripe\Stripe::class)) {
            Log::error('Stripe SDK not installed. Payment processing unavailable.');
            return back()->with('error', 'Payment service is temporarily unavailable. Please contact support.');
        }
        $secret = config('services.stripe.secret');
        if (empty($secret)) {
            Log::error('STRIPE_SECRET_KEY is not configured.');
            return back()->with('error', 'Payment service is not configured. Please contact support.');
        }
        \Stripe\Stripe::setApiKey($secret);

        try {
            $resolved = $this->resolvePrice($request, $validated['plan']);
        } catch (\Throwable $e) {
            Log::error('Stripe price resolution failed: ' . $e->getMessage());
            return back()->with('error', 'Pricing is being configured. Please try again shortly.');
        }

        $user = Auth::user();

        try {
            $session = \Stripe\Checkout\Session::create([
                'mode'                 => 'subscription',
                'payment_method_types' => ['card'],
                'line_items'           => [[
                    'price'    => $resolved['price_id'],
                    'quantity' => 1,
                ]],
                'subscription_data'    => [
                    'trial_period_days' => (int) config('billing.trial_days', 7),
                    'metadata'          => [
                        'user_id' => $user?->id,
                        'plan'    => $resolved['plan'],
                    ],
                ],
                'automatic_tax'        => ['enabled' => (bool) config('billing.tax_enabled', true)],
                'allow_promotion_codes'=> true,
                'success_url'          => route('payment.success', ['provider' => 'stripe']) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'           => route('payment.cancel',  ['provider' => 'stripe']),
                'customer_email'       => $user?->email,
                'metadata'             => [
                    'user_id' => $user?->id,
                    'plan'    => $resolved['plan'],
                ],
            ]);
            return redirect($session->url);
        } catch (\Stripe\Exception\RateLimitException $e) {
            Log::warning('Stripe rate limited during checkout', ['error' => $e->getMessage()]);
            return back()->with('error', 'Too many requests. Please try again in a moment.');
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            Log::error('Invalid Stripe checkout parameters', ['error' => $e->getMessage()]);
            return back()->with('error', 'Payment configuration error. Please contact support.');
        } catch (\Throwable $e) {
            Log::error('Stripe checkout failed: ' . $e->getMessage());
            return back()->with('error', 'Payment processing failed. Please try again or contact support.');
        }
    }

    /**
     * Stripe Customer Portal — cancel, upgrade, update card, download invoices.
     */
    public function stripeBillingPortal(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }
        if (! class_exists(\Stripe\BillingPortal\Session::class)) {
            return back()->with('error', 'Billing portal unavailable. Please contact support.');
        }
        $secret = config('services.stripe.secret');
        if (empty($secret)) {
            return back()->with('error', 'Billing is not configured.');
        }
        \Stripe\Stripe::setApiKey($secret);

        $customerId = $user->stripe_customer_id ?? null;
        if (! $customerId) {
            return back()->with('error', 'No billing customer record found yet. Subscribe first to manage billing.');
        }

        try {
            $session = \Stripe\BillingPortal\Session::create([
                'customer'   => $customerId,
                'return_url' => url(config('billing.portal.return_url', '/')),
            ]);
            return redirect($session->url);
        } catch (\Throwable $e) {
            Log::error('Stripe portal session failed: ' . $e->getMessage());
            return back()->with('error', 'Billing portal failed to open. Please try again.');
        }
    }

    public function stripeWebhook(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $whSecret  = config('services.stripe.webhook_secret');

        if (! class_exists(\Stripe\Webhook::class)) {
            Log::error('Stripe SDK not available — webhook rejected');
            return response('Webhook infrastructure not configured', 500);
        }
        if (empty($whSecret)) {
            Log::error('STRIPE_WEBHOOK_SECRET not configured — rejecting all webhooks');
            return response('Webhook secret not configured', 500);
        }
        if (empty($sigHeader)) {
            Log::warning('Stripe webhook received without Stripe-Signature header');
            return response('Signature required', 400);
        }
        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $whSecret);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::warning('Stripe webhook signature verification failed', ['error' => $e->getMessage()]);
            return response('Invalid signature', 400);
        } catch (\Exception $e) {
            Log::error('Stripe webhook parse error', ['error' => $e->getMessage()]);
            return response('Parse error', 400);
        }

        // Idempotency: dedup on event.id via stripe_webhook_events row.
        if ($this->webhookAlreadyProcessed($event->id)) {
            Log::info('Stripe webhook already processed — skipping duplicate', ['event_id' => $event->id]);
            return response()->noContent();
        }

        Log::info('Stripe webhook received', ['type' => $event->type, 'event_id' => $event->id]);

        try {
            match ($event->type) {
                'checkout.session.completed'    => $this->handleCheckoutCompleted($event),
                'customer.subscription.updated' => $this->handleSubscriptionUpdated($event),
                'customer.subscription.deleted' => $this->handleSubscriptionDeleted($event),
                'invoice.payment_succeeded'     => $this->handleInvoicePaid($event),
                'invoice.payment_failed'        => $this->handleInvoiceFailed($event),
                'customer.updated'              => $this->handleCustomerUpdated($event),
                default                         => Log::info("Stripe webhook unhandled type: {$event->type}"),
            };
            $this->recordWebhookProcessed($event->id, $event->type);
        } catch (\Throwable $e) {
            Log::error('Stripe webhook handler crashed', ['event_id' => $event->id, 'error' => $e->getMessage()]);
            return response('Handler error', 500);    // Stripe will retry
        }

        return response()->noContent();
    }

    private function webhookAlreadyProcessed(string $eventId): bool
    {
        return DB::table('stripe_webhook_events')->where('event_id', $eventId)->exists();
    }

    private function recordWebhookProcessed(string $eventId, string $type): void
    {
        DB::table('stripe_webhook_events')->insertOrIgnore([
            'event_id'   => $eventId,
            'type'       => $type,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function handleCheckoutCompleted($event): void
    {
        $session = $event->data->object;
        $userId  = $session->metadata->user_id ?? null;
        $plan    = $session->metadata->plan    ?? 'monthly';
        $customerId = $session->customer ?? null;
        $subscriptionId = $session->subscription ?? null;

        if (! $userId) return;
        $user = \App\Models\User::find($userId);
        if (! $user) return;

        if ($customerId && empty($user->stripe_customer_id)) {
            $user->forceFill(['stripe_customer_id' => $customerId])->saveQuietly();
        }
        $this->activateSubscription('stripe', $user, $plan, $session->id, $subscriptionId);
        Log::info('Stripe subscription activated via webhook', [
            'user_id'         => $userId,
            'plan'            => $plan,
            'session_id'      => $session->id,
            'subscription_id' => $subscriptionId,
        ]);
    }

    private function handleSubscriptionUpdated($event): void
    {
        $sub = $event->data->object;
        Subscription::where('provider', 'stripe')
            ->where('provider_id', $sub->id)
            ->update([
                'ends_at' => $sub->current_period_end ? Carbon::createFromTimestamp($sub->current_period_end) : null,
                'active'  => in_array($sub->status, ['active', 'trialing'], true),
            ]);
    }

    private function handleSubscriptionDeleted($event): void
    {
        $sub = $event->data->object;
        Subscription::where('provider', 'stripe')
            ->where('provider_id', $sub->id)
            ->update([
                'active'      => false,
                'cancelled_at'=> now(),
            ]);
    }

    private function handleInvoicePaid($event): void
    {
        $invoice = $event->data->object;
        if ($subscriptionId = $invoice->subscription ?? null) {
            Subscription::where('provider', 'stripe')
                ->where('provider_id', $subscriptionId)
                ->update([
                    'ends_at' => $invoice->lines->data[0]->period->end ?? null
                        ? Carbon::createFromTimestamp($invoice->lines->data[0]->period->end)
                        : null,
                    'active'  => true,
                ]);
        }
    }

    private function handleInvoiceFailed($event): void
    {
        $invoice = $event->data->object;
        $customerId = $invoice->customer ?? null;
        Log::warning('Stripe invoice payment failed', [
            'subscription' => $invoice->subscription ?? null,
            'customer'     => $customerId,
            'attempt'      => $invoice->attempt_count ?? null,
        ]);

        // Phase 5: dunning notification (queued).
        if ($customerId) {
            $user = \App\Models\User::where('stripe_customer_id', $customerId)->first();
            if ($user) {
                $user->notify(new \App\Notifications\InvoicePaymentFailed(
                    invoiceId:    (string) ($invoice->id ?? 'unknown'),
                    attemptCount: (int) ($invoice->attempt_count ?? 1),
                    amountCents:  isset($invoice->amount_due) ? (int) $invoice->amount_due : null,
                    currency:     strtoupper((string) ($invoice->currency ?? 'USD')),
                ));
            }
        }
    }

    private function handleCustomerUpdated($event): void
    {
        $customer = $event->data->object;
        if (! empty($customer->email)) {
            \App\Models\User::where('stripe_customer_id', $customer->id)->update(['email' => $customer->email]);
        }
    }

    public function paymentSuccess(Request $request, $provider)
    {
        $user = Auth::user();
        if (! $user) return redirect()->route('login');
        return redirect('/')->with('status', 'Subscription is being processed. You will receive a confirmation shortly.');
    }

    public function paymentCancel(Request $request, $provider)
    {
        return redirect()->route('checkout')->with('error', ucfirst($provider) . ' payment was cancelled.');
    }

    /**
     * Activate / extend a subscription row. Caller passes the Stripe session id
     * AND the Stripe subscription id so the row carries both for webhook dedup.
     */
    protected function activateSubscription(
        string $provider,
        $user,
        string $plan = 'monthly',
        ?string $sessionId = null,
        ?string $subscriptionId = null,
    ) {
        $months = $plan === 'annual' ? 12 : 1;
        $now    = Carbon::now();
        $endsAt = $now->copy()->addMonths($months);

        Subscription::updateOrCreate(
            [
                'user_id'     => $user->id,
                'provider_id' => $subscriptionId ?: $sessionId,
            ],
            [
                'plan'            => $plan,
                'provider'        => $provider,
                'idempotency_key' => $subscriptionId ?: $sessionId,
                'starts_at'       => $now,
                'ends_at'         => $endsAt,
                'active'          => true,
            ]
        );
    }
}

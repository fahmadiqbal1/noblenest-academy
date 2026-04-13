<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\PricingService;
use Illuminate\Http\Request;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function __construct(private readonly PricingService $pricing) {}

    /**
     * Resolve plan type from explicit plan parameter.
     * Falls back to amount heuristic only if plan param is absent.
     */
    private function resolvePlan(int $amountCents, string $planParam = ''): array
    {
        if ($planParam === 'annual') {
            return ['plan' => 'annual', 'months' => 12];
        }
        if ($planParam === 'monthly') {
            return ['plan' => 'monthly', 'months' => 1];
        }
        // Fallback: annual if >= $20 USD equivalent
        if ($amountCents >= 2000) {
            return ['plan' => 'annual', 'months' => 12];
        }
        return ['plan' => 'monthly', 'months' => 1];
    }

    // Stripe checkout — requires SDK and valid secret key. NO fallback to free access.
    public function stripeCheckout(Request $request)
    {
        // SECURITY: Validate all payment parameters server-side
        $validated = $request->validate([
            'amount'      => 'required|integer|min:50|max:999900',
            'currency'    => 'required|string|size:3',
            'description' => 'nullable|string|max:255',
            'plan'        => 'nullable|in:monthly,annual',
        ]);

        $amount   = (int) $validated['amount'];
        $currency = strtoupper($validated['currency']);
        $description = $validated['description'] ?? 'Noble Nest Plan';
        $planParam   = $validated['plan'] ?? '';

        // SECURITY: Require Stripe SDK — no free-access fallback if missing
        if (!class_exists(\Stripe\Checkout\Session::class) || !class_exists(\Stripe\Stripe::class)) {
            Log::error('Stripe SDK not installed. Payment processing unavailable.');
            return back()->with('error', 'Payment service is temporarily unavailable. Please contact support.');
        }

        $secret = config('services.stripe.secret');
        if (empty($secret)) {
            Log::error('STRIPE_SECRET_KEY is not configured.');
            return back()->with('error', 'Payment service is not configured. Please contact support.');
        }

        \Stripe\Stripe::setApiKey($secret);
        $planInfo = $this->resolvePlan($amount, $planParam);

        try {
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency'     => strtolower($currency),
                        'product_data' => ['name' => $description],
                        'unit_amount'  => $amount,
                    ],
                    'quantity' => 1,
                ]],
                'mode'          => 'payment',
                'success_url'   => route('payment.success', ['provider' => 'stripe']) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'    => route('payment.cancel', ['provider' => 'stripe']),
                'customer_email' => optional(Auth::user())->email,
                'metadata' => [
                    'user_id'  => Auth::id(),
                    'plan'     => $planInfo['plan'],
                    'months'   => $planInfo['months'],
                    'amount'   => $amount,
                    'currency' => $currency,
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
        // SECURITY: No fallback — we never reach here, but we never grant free access.
    }

    // Stripe webhook — signature verification is MANDATORY. Unsigned payloads are rejected.
    public function stripeWebhook(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $whSecret  = config('services.stripe.webhook_secret');

        // SECURITY: Reject if SDK is missing or webhook secret is not configured
        if (!class_exists(\Stripe\Webhook::class)) {
            Log::error('Stripe SDK not available — webhook rejected');
            return response('Webhook infrastructure not configured', 500);
        }

        if (empty($whSecret)) {
            Log::error('STRIPE_WEBHOOK_SECRET not configured — rejecting all webhooks to prevent fraud');
            return response('Webhook secret not configured', 500);
        }

        // SECURITY: Reject unsigned payloads
        if (empty($sigHeader)) {
            Log::warning('Stripe webhook received without Stripe-Signature header');
            return response('Signature required', 400);
        }

        // Verify cryptographic signature
        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $whSecret);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::warning('Stripe webhook signature verification failed', ['error' => $e->getMessage()]);
            return response('Invalid signature', 400);
        } catch (\Exception $e) {
            Log::error('Stripe webhook parse error', ['error' => $e->getMessage()]);
            return response('Parse error', 400);
        }

        Log::info('Stripe webhook received', ['type' => $event->type]);

        if ($event->type === 'checkout.session.completed') {
            $session   = $event->data->object;
            $sessionId = $session->id ?? null;

            // IDEMPOTENCY: Reject duplicate webhook deliveries (Stripe retries on 5xx)
            if ($sessionId && Subscription::where('provider', 'stripe')
                    ->where('provider_id', $sessionId)->exists()) {
                Log::info('Stripe webhook already processed — skipping duplicate', ['session_id' => $sessionId]);
                return response()->noContent(); // 204 — idempotent success
            }

            $metadata = $session->metadata;
            if ($metadata) {
                $userId   = $metadata->user_id ?? null;
                $plan     = $metadata->plan ?? 'individual';
                $months   = (int) ($metadata->months ?? 1);
                $amount   = (int) ($metadata->amount ?? 1000);
                $currency = $metadata->currency ?? 'USD';

                if ($userId) {
                    $user = \App\Models\User::find($userId);
                    if ($user) {
                        $this->activateSubscription('stripe', $user, $amount, $currency, $plan, $months, $sessionId);
                        Log::info('Stripe subscription activated via webhook', [
                            'user_id'    => $userId,
                            'plan'       => $plan,
                            'session_id' => $sessionId,
                        ]);
                    }
                }
            }
        }

        return response()->noContent();
    }

    // PayPal checkout — SDK deprecated. Returns user-friendly error. NO free-access fallback.
    public function paypalCheckout(Request $request)
    {
        Log::info('PayPal checkout attempted but not available', ['user_id' => Auth::id()]);
        return back()->with('error', 'PayPal payments are not yet available. Please use a credit or debit card.');
    }

    // Payment success — activates subscription on redirect back from payment provider
    public function paymentSuccess(Request $request, $provider)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // If returning from Stripe, verify the session to get plan details
        if ($provider === 'stripe' && $request->has('session_id') && class_exists(\Stripe\Checkout\Session::class)) {
            $secret = config('services.stripe.secret');
            if (!empty($secret)) {
                try {
                    \Stripe\Stripe::setApiKey($secret);
                    $session  = \Stripe\Checkout\Session::retrieve($request->input('session_id'));
                    $metadata = $session->metadata;
                    $plan     = $metadata->plan ?? 'individual';
                    $months   = (int) ($metadata->months ?? 1);
                    $amount   = (int) ($metadata->amount ?? 1000);
                    $currency = $metadata->currency ?? 'USD';

                    // IDEMPOTENCY: Check if webhook already activated this session
                    if ($session->id && Subscription::where('provider', 'stripe')
                            ->where('provider_id', $session->id)->exists()) {
                        Log::info('Subscription already activated via webhook', ['session_id' => $session->id]);
                        return redirect('/')->with('status', 'Your subscription is active. Welcome to Noble Nest Academy!');
                    }

                    return $this->activateSubscription('stripe', $user, $amount, $currency, $plan, $months, $session->id);
                } catch (\Throwable $e) {
                    Log::error('Stripe session retrieval failed: ' . $e->getMessage());
                    return redirect('/')
                        ->with('error', 'Unable to verify payment. Please contact support with session ID: ' . $request->input('session_id'));
                }
            }
        }

        // Non-Stripe success page without session verification — no free-access fallback
        return redirect('/')->with('status', 'Subscription is being processed. You will be notified when it is active.');
    }

    public function paymentCancel(Request $request, $provider)
    {
        return redirect()->route('checkout')->with('error', ucfirst($provider).' payment was cancelled.');
    }

    protected function activateSubscription(
        string $provider,
        $user,
        int $amountCents = 1000,
        string $currency = 'USD',
        ?string $plan = null,
        ?int $months = null,
        ?string $providerId = null,
    ) {
        if (!$user) {
            return redirect()->route('login');
        }

        $planInfo = $plan ? ['plan' => $plan, 'months' => $months ?? 1]
                         : $this->resolvePlan($amountCents);

        $now = Carbon::now();
        $endsAt = $now->copy()->addMonths($planInfo['months']);

        // IDEMPOTENCY: Include provider_id in match key to prevent duplicate activations
        Subscription::updateOrCreate(
            [
                'user_id'     => $user->id,
                'plan'        => $planInfo['plan'],
                'provider_id' => $providerId,
            ],
            [
                'provider'        => $provider,
                'provider_id'     => $providerId,
                'idempotency_key' => $providerId,
                'amount'          => $amountCents / 100,
                'currency'        => $currency,
                'starts_at'       => $now,
                'ends_at'         => $endsAt,
                'active'          => true,
            ]
        );

        return redirect('/')->with('status', 'Subscription activated! Enjoy Noble Nest Academy.');
    }
}

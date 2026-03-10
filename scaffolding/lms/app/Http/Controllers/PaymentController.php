<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment as PayPalPayment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

/**
 * Payment Controller for Stripe and PayPal integration.
 * 
 * Note: This is scaffolding/template code. Requires packages:
 * - stripe/stripe-php
 * - paypal/rest-api-sdk-php (or srmklive/paypal for Laravel 10+)
 * 
 * @phpstan-ignore-file
 */
class PaymentController extends Controller
{
    // Stripe checkout
    public function stripeCheckout(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $request->currency,
                    'product_data' => [
                        'name' => $request->description,
                    ],
                    'unit_amount' => $request->amount * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('payment.success', ['provider' => 'stripe']),
            'cancel_url' => route('payment.cancel', ['provider' => 'stripe']),
            'customer_email' => Auth::user()->email,
        ]);
        return redirect($session->url);
    }

    /**
     * Handle Stripe webhook with proper signature verification.
     */
    public function stripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        if (!$webhookSecret) {
            Log::error('Stripe webhook secret not configured');
            return response()->json(['error' => 'Webhook secret not configured'], 500);
        }

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $webhookSecret
            );
        } catch (\UnexpectedValueException $e) {
            Log::warning('Invalid Stripe webhook payload', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::warning('Stripe webhook signature verification failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $this->handleSuccessfulCheckout($session);
                break;

            case 'invoice.payment_succeeded':
                $invoice = $event->data->object;
                $this->handleInvoicePaid($invoice);
                break;

            case 'customer.subscription.deleted':
                $subscription = $event->data->object;
                $this->handleSubscriptionCancelled($subscription);
                break;

            default:
                Log::info('Unhandled Stripe event type: ' . $event->type);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle successful checkout session.
     */
    protected function handleSuccessfulCheckout($session): void
    {
        $customerEmail = $session->customer_email ?? null;
        if (!$customerEmail) {
            Log::warning('Checkout session missing customer email', ['session_id' => $session->id]);
            return;
        }

        $user = \App\Models\User::where('email', $customerEmail)->first();
        if (!$user) {
            Log::warning('User not found for checkout session', ['email' => $customerEmail]);
            return;
        }

        // Activate subscription
        Subscription::updateOrCreate(
            ['user_id' => $user->id, 'plan' => 'individual'],
            [
                'provider' => 'stripe',
                'stripe_session_id' => $session->id,
                'amount' => ($session->amount_total ?? 0) / 100,
                'currency' => strtoupper($session->currency ?? 'USD'),
                'starts_at' => now(),
                'ends_at' => now()->addMonth(),
                'active' => true,
            ]
        );

        Log::info('Subscription activated via Stripe webhook', ['user_id' => $user->id]);
    }

    /**
     * Handle invoice payment success (for recurring subscriptions).
     */
    protected function handleInvoicePaid($invoice): void
    {
        $customerEmail = $invoice->customer_email ?? null;
        if (!$customerEmail) {
            return;
        }

        $user = \App\Models\User::where('email', $customerEmail)->first();
        if (!$user) {
            return;
        }

        // Extend subscription
        $subscription = Subscription::where('user_id', $user->id)->first();
        if ($subscription) {
            $subscription->update([
                'ends_at' => now()->addMonth(),
                'active' => true,
            ]);
            Log::info('Subscription extended via invoice payment', ['user_id' => $user->id]);
        }
    }

    /**
     * Handle subscription cancellation.
     */
    protected function handleSubscriptionCancelled($stripeSubscription): void
    {
        $customerEmail = $stripeSubscription->customer_email ?? null;
        // Note: May need to fetch customer from Stripe API if email not on subscription object
        if (!$customerEmail) {
            Log::info('Subscription cancelled but no email available');
            return;
        }

        $user = \App\Models\User::where('email', $customerEmail)->first();
        if ($user) {
            Subscription::where('user_id', $user->id)->update(['active' => false]);
            Log::info('Subscription deactivated', ['user_id' => $user->id]);
        }
    }

    // PayPal checkout
    public function paypalCheckout(Request $request)
    {
        $apiContext = new ApiContext(new OAuthTokenCredential(
            config('services.paypal.client_id'),
            config('services.paypal.secret')
        ));
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        $amount = new Amount();
        $amount->setCurrency($request->currency)
            ->setTotal($request->amount);
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setDescription($request->description);
        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(route('payment.success', ['provider' => 'paypal']))
            ->setCancelUrl(route('payment.cancel', ['provider' => 'paypal']));
        $payment = new PayPalPayment();
        $payment->setIntent('sale')
            ->setPayer($payer)
            ->setTransactions([$transaction])
            ->setRedirectUrls($redirectUrls);
        try {
            $payment->create($apiContext);
            return redirect($payment->getApprovalLink());
        } catch (\Exception $e) {
            Log::error('PayPal error: ' . $e->getMessage());
            return back()->with('error', 'Payment could not be processed.');
        }
    }

    // PayPal success
    public function paypalSuccess(Request $request)
    {
        // Handle PayPal payment execution and update status
        // ...
    }

    // Payment success/cancel
    public function paymentSuccess(Request $request, $provider)
    {
        $user = Auth::user();
        // For demo: 1 month subscription, plan 'individual'. Adjust as needed.
        $plan = 'individual';
        $amount = 100; // Should be dynamic in production
        $currency = 'USD';
        $now = now();
        $endsAt = $now->copy()->addMonth();
        // Create or update subscription
        $subscription = Subscription::updateOrCreate(
            ['user_id' => $user->id, 'plan' => $plan],
            [
                'provider' => $provider,
                'amount' => $amount,
                'currency' => $currency,
                'starts_at' => $now,
                'ends_at' => $endsAt,
                'active' => true,
            ]
        );
        return redirect('/')->with('success', 'Subscription activated!');
    }
    public function paymentCancel(Request $request, $provider)
    {
        // Handle payment cancellation
        // ...
    }
}

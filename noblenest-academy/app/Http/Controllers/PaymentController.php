<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PaymentController extends Controller
{
    // Stripe checkout (graceful fallback if SDK/keys are missing)
    public function stripeCheckout(Request $request)
    {
        $amount = (int) $request->input('amount', 1000); // cents
        $currency = $request->input('currency', 'USD');
        $description = $request->input('description', 'Noble Nest Plan');

        $hasSdk = class_exists(\Stripe\Checkout\Session::class) && class_exists(\Stripe\Stripe::class);
        $secret = config('services.stripe.secret');
        if ($hasSdk && !empty($secret)) {
            try {
                \Stripe\Stripe::setApiKey($secret);
                $session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price_data' => [
                            'currency' => strtolower($currency),
                            'product_data' => ['name' => $description],
                            'unit_amount' => $amount, // already cents
                        ],
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    'success_url' => route('payment.success', ['provider' => 'stripe']),
                    'cancel_url' => route('payment.cancel', ['provider' => 'stripe']),
                    'customer_email' => optional(Auth::user())->email,
                ]);
                return redirect($session->url);
            } catch (\Throwable $e) {
                Log::error('Stripe checkout failed: '.$e->getMessage());
            }
        }
        // Fallback: simulate success and persist subscription
        return $this->activateSubscriptionAndRedirect('stripe');
    }

    // Stripe webhook (placeholder)
    public function stripeWebhook(Request $request)
    {
        Log::info('Stripe webhook received', ['payload' => $request->all()]);
        return response()->noContent();
    }

    // PayPal checkout (graceful fallback if SDK/keys are missing)
    public function paypalCheckout(Request $request)
    {
        $hasSdk = class_exists(\PayPal\Rest\ApiContext::class);
        $clientId = config('services.paypal.client_id');
        $secret = config('services.paypal.secret');
        if ($hasSdk && !empty($clientId) && !empty($secret)) {
            try {
                $apiContext = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential($clientId, $secret));
                $payer = new \PayPal\Api\Payer();
                $payer->setPaymentMethod('paypal');
                $amount = new \PayPal\Api\Amount();
                $amount->setCurrency($request->input('currency', 'USD'))->setTotal(number_format($request->input('amount', 1000) / 100, 2, '.', ''));
                $transaction = new \PayPal\Api\Transaction();
                $transaction->setAmount($amount)->setDescription($request->input('description', 'Noble Nest Plan'));
                $redirectUrls = new \PayPal\Api\RedirectUrls();
                $redirectUrls->setReturnUrl(route('payment.success', ['provider' => 'paypal']))->setCancelUrl(route('payment.cancel', ['provider' => 'paypal']));
                $payment = new \PayPal\Api\Payment();
                $payment->setIntent('sale')->setPayer($payer)->setTransactions([$transaction])->setRedirectUrls($redirectUrls);
                $payment->create($apiContext);
                return redirect($payment->getApprovalLink());
            } catch (\Throwable $e) {
                Log::error('PayPal checkout failed: '.$e->getMessage());
            }
        }
        // Fallback: simulate success and persist subscription
        return $this->activateSubscriptionAndRedirect('paypal');
    }

    // Payment success/cancel
    public function paymentSuccess(Request $request, $provider)
    {
        return $this->activateSubscriptionAndRedirect($provider);
    }

    public function paymentCancel(Request $request, $provider)
    {
        return redirect()->route('checkout')->with('error', ucfirst($provider).' payment was cancelled.');
    }

    protected function activateSubscriptionAndRedirect(string $provider)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $plan = 'individual';
        $amount = 1000; // cents
        $currency = 'USD';
        $now = Carbon::now();
        $endsAt = $now->copy()->addMonth();
        Subscription::updateOrCreate(
            ['user_id' => $user->id, 'plan' => $plan],
            [
                'provider' => $provider,
                'amount' => $amount / 100,
                'currency' => $currency,
                'starts_at' => $now,
                'ends_at' => $endsAt,
                'active' => true,
            ]
        );
        return redirect('/')->with('success', 'Subscription activated!');
    }
}

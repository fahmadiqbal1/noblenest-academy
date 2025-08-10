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
use Carbon\Carbon;

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

    // Stripe webhook
    public function stripeWebhook(Request $request)
    {
        // Handle webhook logic here (e.g., update payment status)
        // ...
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
        $now = Carbon::now();
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

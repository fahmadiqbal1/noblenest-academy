<?php

namespace App\Http\Controllers;

use App\Services\PayPalCheckoutService;
use App\Services\PricingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Phase 7 — PayPal v2 Orders scaffold endpoints.
 *
 * In MVP the service returns stub responses when PayPal creds are absent,
 * so these endpoints are safe to expose immediately.
 */
class PayPalController extends Controller
{
    public function __construct(
        protected PayPalCheckoutService $paypal,
        protected PricingService $pricing,
    ) {}

    public function create(Request $request): JsonResponse
    {
        $data = $request->validate([
            'plan' => 'required|string|max:40',
            'country' => 'nullable|string|size:2',
        ]);

        $tier = $this->pricing->resolveTier($data['plan'], $data['country'] ?? null);
        $amount = $tier->effectivePrice();
        $order = $this->paypal->createOrder($amount, $tier->currency_code ?? 'USD');

        $user = Auth::user();
        if ($user) {
            DB::table('paypal_transactions')->insert([
                'user_id' => $user->id,
                'paypal_order_id' => $order['id'],
                'plan' => $data['plan'],
                'amount' => (int) round($amount * 100),
                'currency' => strtoupper($tier->currency_code ?? 'USD'),
                'status' => 'created',
                'raw_response' => json_encode($order),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json($order);
    }

    public function capture(Request $request, string $orderId): JsonResponse
    {
        $result = $this->paypal->captureOrder($orderId);

        DB::table('paypal_transactions')
            ->where('paypal_order_id', $orderId)
            ->update([
                'status' => strtolower($result['status']),
                'paypal_capture_id' => $result['id'],
                'raw_response' => json_encode($result),
                'updated_at' => now(),
            ]);

        return response()->json($result);
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $headers = collect($request->headers->all())
            ->mapWithKeys(fn (array $v, string $k) => [strtolower($k) => (string) ($v[0] ?? '')])
            ->toArray();

        if (! $this->paypal->verifyWebhook($payload, $headers)) {
            Log::warning('PayPal webhook signature verification failed');

            return response('Invalid signature', 400);
        }

        Log::info('PayPal webhook received', ['type' => $request->input('event_type')]);

        return response()->noContent();
    }
}

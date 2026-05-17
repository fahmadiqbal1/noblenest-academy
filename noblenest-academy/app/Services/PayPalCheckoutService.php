<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Phase 7 — PayPal v2 Orders API scaffold.
 *
 * MVP behavior:
 *   - When PAYPAL_CLIENT_ID / PAYPAL_SECRET are empty, every method returns
 *     a deterministic stub response. This lets tests + local dev run without
 *     real keys; Phase 12/13 ops will inject the real credentials.
 *   - When creds are present, hits the real PayPal v2 Orders API via Http.
 */
class PayPalCheckoutService
{
    public function __construct(
        protected ?string $clientId = null,
        protected ?string $clientSecret = null,
        protected ?string $webhookId = null,
        protected string $env = 'sandbox',
    ) {
        $this->clientId = $clientId ?? (string) config('services.paypal.client_id');
        $this->clientSecret = $clientSecret ?? (string) config('services.paypal.secret');
        $this->webhookId = $webhookId ?? (string) config('services.paypal.webhook_id');
        $this->env = (string) (config('services.paypal.env') ?? config('services.paypal.mode') ?? 'sandbox');
    }

    public function isStubMode(): bool
    {
        return empty($this->clientId) || empty($this->clientSecret);
    }

    public function baseUrl(): string
    {
        return $this->env === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    /**
     * Create a PayPal v2 order.
     *
     * @return array{id: string, status: string, links?: array, stub?: bool}
     */
    public function createOrder(float $amount, string $currency = 'USD'): array
    {
        if ($this->isStubMode()) {
            return [
                'id' => 'STUB-'.strtoupper(bin2hex(random_bytes(8))),
                'status' => 'CREATED',
                'stub' => true,
                'amount' => number_format($amount, 2, '.', ''),
                'currency_code' => strtoupper($currency),
            ];
        }

        $token = $this->accessToken();
        $resp = Http::withToken($token)
            ->asJson()
            ->acceptJson()
            ->post($this->baseUrl().'/v2/checkout/orders', [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => strtoupper($currency),
                        'value' => number_format($amount, 2, '.', ''),
                    ],
                ]],
            ]);

        if (! $resp->successful()) {
            Log::warning('PayPal createOrder failed', ['status' => $resp->status(), 'body' => $resp->body()]);
            throw new \RuntimeException('PayPal createOrder failed: '.$resp->status());
        }

        return $resp->json();
    }

    /**
     * Capture an approved order.
     *
     * @return array{id: string, status: string, stub?: bool}
     */
    public function captureOrder(string $orderId): array
    {
        if ($this->isStubMode()) {
            return [
                'id' => $orderId,
                'status' => 'COMPLETED',
                'stub' => true,
            ];
        }

        $token = $this->accessToken();
        $resp = Http::withToken($token)
            ->asJson()
            ->acceptJson()
            ->post($this->baseUrl()."/v2/checkout/orders/{$orderId}/capture");

        if (! $resp->successful()) {
            Log::warning('PayPal captureOrder failed', ['status' => $resp->status(), 'body' => $resp->body()]);
            throw new \RuntimeException('PayPal captureOrder failed: '.$resp->status());
        }

        return $resp->json();
    }

    /**
     * Verify a webhook payload against the configured webhook_id.
     *
     * MVP: in stub mode we accept any payload (returns true) so local tests pass.
     * Real verification uses PayPal's notifications/verify-webhook-signature API.
     */
    public function verifyWebhook(string $payload, array $headers): bool
    {
        if ($this->isStubMode()) {
            return true;
        }

        $token = $this->accessToken();

        try {
            $resp = Http::withToken($token)
                ->asJson()
                ->acceptJson()
                ->post($this->baseUrl().'/v1/notifications/verify-webhook-signature', [
                    'auth_algo' => $headers['paypal-auth-algo'] ?? $headers['PAYPAL-AUTH-ALGO'] ?? '',
                    'cert_url' => $headers['paypal-cert-url'] ?? $headers['PAYPAL-CERT-URL'] ?? '',
                    'transmission_id' => $headers['paypal-transmission-id'] ?? $headers['PAYPAL-TRANSMISSION-ID'] ?? '',
                    'transmission_sig' => $headers['paypal-transmission-sig'] ?? $headers['PAYPAL-TRANSMISSION-SIG'] ?? '',
                    'transmission_time' => $headers['paypal-transmission-time'] ?? $headers['PAYPAL-TRANSMISSION-TIME'] ?? '',
                    'webhook_id' => $this->webhookId,
                    'webhook_event' => json_decode($payload, true),
                ]);
        } catch (\Throwable $e) {
            Log::warning('PayPal webhook verify call threw', ['error' => $e->getMessage()]);

            return false;
        }

        return $resp->successful()
            && ($resp->json('verification_status') === 'SUCCESS');
    }

    protected function accessToken(): string
    {
        $cacheKey = 'paypal_access_token:'.md5($this->clientId.$this->env);

        return Cache::remember($cacheKey, 540, function () {
            $resp = Http::asForm()
                ->withBasicAuth($this->clientId, $this->clientSecret)
                ->acceptJson()
                ->post($this->baseUrl().'/v1/oauth2/token', [
                    'grant_type' => 'client_credentials',
                ]);

            if (! $resp->successful()) {
                throw new \RuntimeException('PayPal OAuth failed: '.$resp->status());
            }

            return (string) $resp->json('access_token');
        });
    }
}

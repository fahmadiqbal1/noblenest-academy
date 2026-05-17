<?php

namespace App\Services;

use App\Models\PricingTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * PricingService — Geo-detected tiered pricing + Phase 7 spec plans + PPP.
 *
 * Three responsibilities:
 *
 *  1. Region-based tier resolution (legacy region_code rows) — used by
 *     PaymentController to pick a Stripe price_id for the user's region.
 *
 *  2. Plan-key + country resolution (Phase 7 PPP) — given a plan key
 *     ('individual', 'family', ...) and an optional ISO-2 country, return
 *     the PPP-adjusted PricingTier when one exists, otherwise the global row.
 *
 *  3. Proration math for in-flight plan upgrades.
 */
class PricingService
{
    private const FALLBACK_TIERS = [
        'GLOBAL_SOUTH' => [
            'region_label'  => 'Global South',
            'country_codes' => ['NG', 'GH', 'KE', 'TZ', 'ET', 'ZM', 'UG', 'RW', 'SN', 'CI', 'CM', 'MZ', 'BO', 'PE', 'EC', 'PY', 'VN', 'MM', 'KH', 'LA'],
            'price_monthly' => 2.99,
            'price_yearly'  => 29.99,
            'currency_code' => 'USD',
        ],
        'SOUTH_ASIA' => [
            'region_label'  => 'South Asia',
            'country_codes' => ['PK', 'BD', 'LK', 'NP'],
            'price_monthly' => 2.99,
            'price_yearly'  => 28.70,
            'currency_code' => 'USD',
        ],
        'INDIA' => [
            'region_label'  => 'India',
            'country_codes' => ['IN'],
            'price_monthly' => 2.99,
            'price_yearly'  => 28.70,
            'currency_code' => 'USD',
        ],
        'SEA' => [
            'region_label'  => 'Southeast Asia',
            'country_codes' => ['ID', 'MY', 'PH', 'TH', 'SG'],
            'price_monthly' => 3.99,
            'price_yearly'  => 38.30,
            'currency_code' => 'USD',
        ],
        'EUROPE' => [
            'region_label'  => 'Europe',
            'country_codes' => ['GB', 'DE', 'FR', 'IT', 'ES', 'PL', 'NL', 'BE', 'SE', 'NO', 'DK', 'FI', 'PT', 'CZ', 'HU', 'RO', 'GR', 'RU', 'TR'],
            'price_monthly' => 4.99,
            'price_yearly'  => 47.90,
            'currency_code' => 'USD',
        ],
        'MENA' => [
            'region_label'  => 'Middle East & North Africa',
            'country_codes' => ['SA', 'AE', 'QA', 'KW', 'BH', 'OM', 'JO', 'LB', 'EG', 'MA', 'TN', 'DZ', 'IQ', 'SY', 'YE', 'LY'],
            'price_monthly' => 6.99,
            'price_yearly'  => 67.10,
            'currency_code' => 'USD',
        ],
        'GLOBAL' => [
            'region_label'  => 'Global',
            'country_codes' => [],
            'price_monthly' => 4.99,
            'price_yearly'  => 47.90,
            'currency_code' => 'USD',
        ],
    ];

    /**
     * Resolve the pricing tier for the current request (region-based, array shape).
     */
    public function resolve(Request $request): array
    {
        $country = $this->detectCountry($request);
        return $this->tierForCountry($country);
    }

    /**
     * Polymorphic resolveTier:
     *
     *   resolveTier(Request)                       → PricingTier|null  (region tier)
     *   resolveTier(string $tierKey, ?string $cc)  → PricingTier       (spec plan, PPP-adjusted)
     */
    public function resolveTier(Request|string $arg, ?string $countryCode = null): ?PricingTier
    {
        // ── Phase 7 spec-plan path ───────────────────────────────────────────
        if (is_string($arg)) {
            $tierKey = $arg;

            if ($countryCode !== null) {
                $countryCode = strtoupper($countryCode);
                $ppp = PricingTier::query()
                    ->where('key', $tierKey)
                    ->where('country_code', $countryCode)
                    ->where('is_active', true)
                    ->first();
                if ($ppp) {
                    return $ppp;
                }
            }

            $base = PricingTier::query()
                ->where('key', $tierKey)
                ->whereNull('country_code')
                ->where('is_active', true)
                ->first();

            if ($base) {
                return $base;
            }

            throw new \RuntimeException("Unknown pricing tier key: {$tierKey}. Run db:seed --class=PricingTierSeeder.");
        }

        // ── Legacy region-based path (used by PaymentController) ─────────────
        $country = $this->detectCountry($arg);
        if (! $country) {
            return PricingTier::query()
                ->whereNull('key')
                ->where('region_code', 'GLOBAL')
                ->where('is_active', true)
                ->first();
        }

        return PricingTier::query()
            ->whereNull('key')
            ->where('is_active', true)
            ->get()
            ->first(fn ($t) => in_array($country, $t->country_codes ?? [], true));
    }

    /**
     * Phase 7 — Resolve country code from the request.
     *
     * Priority:
     *   1. Cloudflare CF-IPCountry header.
     *   2. MaxMind GeoIP2 stub (returns null in MVP, wired Phase 12/13).
     *   3. null.
     *
     * Cached in session for 24 h.
     */
    public function resolveCountryFromRequest(Request $request): ?string
    {
        if ($request->hasSession()) {
            $cached  = $request->session()->get('resolved_country');
            $expires = $request->session()->get('resolved_country_expires_at');
            if ($cached !== null && $expires !== null && $expires > time()) {
                return $cached ?: null;
            }
        }

        $country = null;
        $cf = strtoupper(trim((string) $request->header('CF-IPCountry', '')));
        if (strlen($cf) === 2 && ctype_alpha($cf)) {
            $country = $cf;
        }

        if ($country === null) {
            $country = $this->maxMindLookup($request->ip());
        }

        if ($request->hasSession()) {
            $request->session()->put('resolved_country', $country ?? '');
            $request->session()->put('resolved_country_expires_at', time() + 86400);
        }

        return $country;
    }

    /**
     * MaxMind GeoIP2 lookup stub — Phase 12/13 wires the actual DB.
     */
    protected function maxMindLookup(?string $ip): ?string
    {
        return null;
    }

    /**
     * Compute the prorated charge for an in-flight plan upgrade.
     *
     * @return array{amount: float, currency: string, days_remaining: int, cycle_days: int}
     */
    public function computeUpgradeProration(
        PricingTier $oldTier,
        PricingTier $newTier,
        int $daysRemaining,
        int $cycleDays = 30,
    ): array {
        $daysRemaining = max(0, min($daysRemaining, $cycleDays));

        $oldPrice = $oldTier->effectivePrice();
        $newPrice = $newTier->effectivePrice();

        $perDayDelta = ($newPrice - $oldPrice) / $cycleDays;
        $amount = round(max(0.0, $perDayDelta * $daysRemaining), 2);

        return [
            'amount'         => $amount,
            'currency'       => $newTier->currency_code ?? 'USD',
            'days_remaining' => $daysRemaining,
            'cycle_days'     => $cycleDays,
        ];
    }

    /**
     * Detect 2-letter ISO country code from request (legacy path).
     */
    public function detectCountry(Request $request): ?string
    {
        $cf = strtoupper(trim((string) $request->header('CF-IPCountry', '')));
        if (strlen($cf) === 2 && ctype_alpha($cf)) {
            return $cf;
        }

        $custom = strtoupper(trim((string) $request->header('X-Country-Code', '')));
        if (strlen($custom) === 2 && ctype_alpha($custom)) {
            return $custom;
        }

        if ($request->user() && $request->user()->country_code) {
            return strtoupper($request->user()->country_code);
        }

        return null;
    }

    /**
     * Get pricing tier data for a given country code (legacy array shape).
     */
    public function tierForCountry(?string $country): array
    {
        if (! $country) {
            return array_merge(self::FALLBACK_TIERS['GLOBAL'], ['region_code' => 'GLOBAL']);
        }

        return Cache::remember("pricing_tier:{$country}", 3600, function () use ($country) {
            $dbTier = PricingTier::where('is_active', true)
                ->whereNull('key')
                ->get()
                ->first(fn ($t) => in_array($country, $t->country_codes ?? [], true));

            if ($dbTier) {
                return $dbTier->toArray();
            }

            foreach (self::FALLBACK_TIERS as $code => $tier) {
                if (in_array($country, $tier['country_codes'], true)) {
                    return array_merge($tier, ['region_code' => $code]);
                }
            }

            return array_merge(self::FALLBACK_TIERS['GLOBAL'], ['region_code' => 'GLOBAL']);
        });
    }
}

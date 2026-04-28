<?php

namespace App\Services;

use App\Models\PricingTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * PricingService — Geo-detected tiered pricing.
 *
 * Priority order for country detection:
 *  1. Cloudflare CF-IPCountry header (most reliable on CF-proxied production)
 *  2. X-Country-Code header (set by load balancer in dev/staging)
 *  3. User's stored country_code (set at registration)
 *  4. Default to Global (US pricing)
 */
class PricingService
{
    /**
     * Default pricing tiers seeded in the DB.
     * Used for in-memory fallback if DB not yet seeded.
     */
    private const FALLBACK_TIERS = [
        'GLOBAL_SOUTH' => [
            'region_label'   => 'Global South',
            'country_codes'  => ['NG', 'GH', 'KE', 'TZ', 'ET', 'ZM', 'UG', 'RW', 'SN', 'CI', 'CM', 'MZ', 'BO', 'PE', 'EC', 'PY', 'VN', 'MM', 'KH', 'LA'],
            'price_monthly'  => 2.99,
            'price_yearly'   => 29.99,
            'currency_code'  => 'USD',
        ],
        'SOUTH_ASIA' => [
            'region_label'   => 'South Asia',
            'country_codes'  => ['PK', 'BD', 'LK', 'NP'],
            'price_monthly'  => 2.99,
            'price_yearly'   => 28.70,
            'currency_code'  => 'USD',
        ],
        'INDIA' => [
            'region_label'   => 'India',
            'country_codes'  => ['IN'],
            'price_monthly'  => 2.99,
            'price_yearly'   => 28.70,
            'currency_code'  => 'USD',
        ],
        'SEA' => [
            'region_label'   => 'Southeast Asia',
            'country_codes'  => ['ID', 'MY', 'PH', 'TH', 'SG'],
            'price_monthly'  => 3.99,
            'price_yearly'   => 38.30,
            'currency_code'  => 'USD',
        ],
        'EUROPE' => [
            'region_label'   => 'Europe',
            'country_codes'  => ['GB', 'DE', 'FR', 'IT', 'ES', 'PL', 'NL', 'BE', 'SE', 'NO', 'DK', 'FI', 'PT', 'CZ', 'HU', 'RO', 'GR', 'RU', 'TR'],
            'price_monthly'  => 4.99,
            'price_yearly'   => 47.90,
            'currency_code'  => 'USD',
        ],
        'MENA' => [
            'region_label'   => 'Middle East & North Africa',
            'country_codes'  => ['SA', 'AE', 'QA', 'KW', 'BH', 'OM', 'JO', 'LB', 'EG', 'MA', 'TN', 'DZ', 'IQ', 'SY', 'YE', 'LY'],
            'price_monthly'  => 6.99,
            'price_yearly'   => 67.10,
            'currency_code'  => 'USD',
        ],
        'GLOBAL' => [
            'region_label'   => 'Global',
            'country_codes'  => [],
            'price_monthly'  => 4.99,
            'price_yearly'   => 47.90,
            'currency_code'  => 'USD',
        ],
    ];

    /**
     * Resolve the pricing tier for the current request.
     */
    public function resolve(Request $request): array
    {
        $country = $this->detectCountry($request);
        return $this->tierForCountry($country);
    }

    /**
     * Detect 2-letter ISO country code from request.
     */
    public function detectCountry(Request $request): ?string
    {
        // 1. Cloudflare header
        $cf = strtoupper(trim($request->header('CF-IPCountry', '')));
        if (strlen($cf) === 2 && ctype_alpha($cf)) {
            return $cf;
        }

        // 2. Custom load-balancer header
        $custom = strtoupper(trim($request->header('X-Country-Code', '')));
        if (strlen($custom) === 2 && ctype_alpha($custom)) {
            return $custom;
        }

        // 3. Auth user's stored country
        if ($request->user() && $request->user()->country_code) {
            return strtoupper($request->user()->country_code);
        }

        return null;
    }

    /**
     * Get pricing tier data for a given country code.
     */
    public function tierForCountry(?string $country): array
    {
        if (!$country) {
            return array_merge(self::FALLBACK_TIERS['GLOBAL'], ['region_code' => 'GLOBAL']);
        }

        // Cache tier lookup for 60 minutes per country
        return Cache::remember("pricing_tier:{$country}", 3600, function () use ($country) {
            // Try DB first
            $dbTier = PricingTier::where('is_active', true)
                ->get()
                ->first(fn($t) => in_array($country, $t->country_codes ?? [], true));

            if ($dbTier) {
                return $dbTier->toArray();
            }

            // Fallback to in-memory config
            foreach (self::FALLBACK_TIERS as $code => $tier) {
                if (in_array($country, $tier['country_codes'], true)) {
                    return array_merge($tier, ['region_code' => $code]);
                }
            }

            return array_merge(self::FALLBACK_TIERS['GLOBAL'], ['region_code' => 'GLOBAL']);
        });
    }
}

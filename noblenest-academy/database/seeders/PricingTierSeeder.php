<?php

namespace Database\Seeders;

use App\Models\PricingTier;
use Illuminate\Database\Seeder;

class PricingTierSeeder extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────────────
        // Legacy region-based tiers (kept for PaymentController.resolvePrice
        // until the Stripe price-sync workflow migrates to spec-plan keys).
        // ─────────────────────────────────────────────────────────────────────
        $regionTiers = [
            [
                'region_code' => 'GLOBAL_SOUTH',
                'region_label' => 'Global South',
                'country_codes' => ['NG', 'GH', 'KE', 'TZ', 'UG', 'ET', 'ZW', 'ZM', 'MW', 'MZ', 'CM', 'SN', 'CI', 'SD', 'LY'],
                'price_monthly' => 2.99,
                'price_yearly' => 28.79,
                'currency_code' => 'USD',
                'is_active' => true,
            ],
            [
                'region_code' => 'SOUTH_ASIA',
                'region_label' => 'South Asia',
                'country_codes' => ['PK', 'BD', 'LK', 'NP', 'AF'],
                'price_monthly' => 2.99,
                'price_yearly' => 28.70,
                'currency_code' => 'USD',
                'is_active' => true,
            ],
            [
                'region_code' => 'INDIA',
                'region_label' => 'India',
                'country_codes' => ['IN'],
                'price_monthly' => 2.99,
                'price_yearly' => 28.70,
                'currency_code' => 'USD',
                'is_active' => true,
            ],
            [
                'region_code' => 'SEA',
                'region_label' => 'Southeast Asia',
                'country_codes' => ['ID', 'MY', 'PH', 'VN', 'TH', 'MM', 'KH', 'LA'],
                'price_monthly' => 3.99,
                'price_yearly' => 38.30,
                'currency_code' => 'USD',
                'is_active' => true,
            ],
            [
                'region_code' => 'MENA',
                'region_label' => 'Middle East & North Africa',
                'country_codes' => ['SA', 'AE', 'QA', 'KW', 'BH', 'OM', 'JO', 'LB', 'IQ', 'SY', 'YE', 'EG', 'MA', 'TN', 'DZ'],
                'price_monthly' => 6.99,
                'price_yearly' => 67.10,
                'currency_code' => 'USD',
                'is_active' => true,
            ],
            [
                'region_code' => 'EUROPE',
                'region_label' => 'Europe',
                'country_codes' => ['GB', 'DE', 'FR', 'NL', 'BE', 'IT', 'ES', 'SE', 'NO', 'DK', 'FI', 'CH', 'AT', 'PT', 'IE', 'PL', 'CZ', 'HU'],
                'price_monthly' => 4.99,
                'price_yearly' => 47.90,
                'currency_code' => 'USD',
                'is_active' => true,
            ],
            [
                'region_code' => 'GLOBAL',
                'region_label' => 'Global',
                'country_codes' => [],
                'price_monthly' => 4.99,
                'price_yearly' => 47.90,
                'currency_code' => 'USD',
                'is_active' => true,
            ],
        ];

        foreach ($regionTiers as $tier) {
            PricingTier::updateOrCreate(
                ['region_code' => $tier['region_code']],
                array_merge($tier, ['key' => null, 'country_code' => null])
            );
        }

        // ─────────────────────────────────────────────────────────────────────
        // Phase 7 spec plans (keyed). Rendered on /pricing and referenced by
        // Subscription.plan + PricingService::resolveTier.
        // ─────────────────────────────────────────────────────────────────────
        $specPlans = [
            [
                'key' => 'freemium',
                'region_code' => 'PLAN_FREEMIUM',
                'region_label' => 'Freemium',
                'price_monthly' => 0.00,
                'price_yearly' => 0.00,
                'interval' => 'monthly',
                'features' => ['1 child seat', '1 locale', '3 activities/day', 'no AI suggestions'],
            ],
            [
                'key' => 'individual',
                'region_code' => 'PLAN_INDIVIDUAL',
                'region_label' => 'Individual',
                'price_monthly' => 12.00,
                'price_yearly' => 0.00,
                'interval' => 'monthly',
                'features' => ['1 child seat', 'all 8 locales', 'unlimited activities', 'AI suggestions'],
            ],
            [
                'key' => 'family',
                'region_code' => 'PLAN_FAMILY',
                'region_label' => 'Family',
                'price_monthly' => 25.00,
                'price_yearly' => 0.00,
                'interval' => 'monthly',
                'features' => ['3 child seats', 'all 8 locales', 'unlimited activities', 'AI suggestions', 'GDPR export'],
            ],
            [
                'key' => 'annual',
                'region_code' => 'PLAN_ANNUAL',
                'region_label' => 'Annual (Family)',
                'price_monthly' => 0.00,
                'price_yearly' => 240.00,
                'interval' => 'yearly',
                'features' => ['3 child seats', 'all 8 locales', 'unlimited activities', 'AI suggestions', 'GDPR export', '~15% off Family'],
            ],
            [
                'key' => 'institutional',
                'region_code' => 'PLAN_INSTITUTIONAL',
                'region_label' => 'Institutional',
                'price_monthly' => 0.00,
                'price_yearly' => 0.00,
                'interval' => 'per_seat',
                'features' => ['Per-seat licensing', 'school_admin role', 'SSO-ready (Phase 8)'],
            ],
        ];

        foreach ($specPlans as $plan) {
            PricingTier::updateOrCreate(
                ['key' => $plan['key'], 'country_code' => null],
                array_merge($plan, [
                    'country_codes' => [],
                    'currency_code' => 'USD',
                    'is_active' => true,
                ])
            );
        }

        // ─────────────────────────────────────────────────────────────────────
        // PPP variants — per-country overrides. Spec multipliers:
        //   IN 0.30, BR 0.40, NG 0.30, PK 0.25, ID 0.35.
        // Only individual / family / annual receive PPP rows (freemium and
        // institutional pricing isn't country-adjusted).
        // ─────────────────────────────────────────────────────────────────────
        $pppMultipliers = [
            'IN' => 0.30,
            'BR' => 0.40,
            'NG' => 0.30,
            'PK' => 0.25,
            'ID' => 0.35,
        ];

        $pppPlans = [
            ['key' => 'individual', 'base_monthly' => 12.00, 'base_yearly' => 0.00,   'interval' => 'monthly'],
            ['key' => 'family',     'base_monthly' => 25.00, 'base_yearly' => 0.00,   'interval' => 'monthly'],
            ['key' => 'annual',     'base_monthly' => 0.00,  'base_yearly' => 240.00, 'interval' => 'yearly'],
        ];

        foreach ($pppPlans as $plan) {
            foreach ($pppMultipliers as $country => $mult) {
                PricingTier::updateOrCreate(
                    ['key' => $plan['key'], 'country_code' => $country],
                    [
                        'region_code' => 'PPP_'.strtoupper($plan['key']).'_'.$country,
                        'region_label' => ucfirst($plan['key']).' ('.$country.' PPP)',
                        'country_codes' => [$country],
                        'price_monthly' => round($plan['base_monthly'] * $mult, 2),
                        'price_yearly' => round($plan['base_yearly'] * $mult, 2),
                        'interval' => $plan['interval'],
                        'features' => null,
                        'currency_code' => 'USD',
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}

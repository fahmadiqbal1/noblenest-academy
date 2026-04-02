<?php

namespace Database\Seeders;

use App\Models\PricingTier;
use Illuminate\Database\Seeder;

class PricingTierSeeder extends Seeder
{
    public function run(): void
    {
        $tiers = [
            [
                'region_code'    => 'GLOBAL_SOUTH',
                'region_label'   => 'Global South',
                'country_codes'  => ['NG', 'GH', 'KE', 'TZ', 'UG', 'ET', 'ZW', 'ZM', 'MW', 'MZ', 'CM', 'SN', 'CI', 'SD', 'LY'],
                'price_monthly'  => 2.99,
                'price_yearly'   => 28.79,
                'currency_code'  => 'USD',
                'stripe_price_id_monthly' => null,
                'stripe_price_id_yearly'  => null,
                'is_active'      => true,
            ],
            [
                'region_code'    => 'SOUTH_ASIA',
                'region_label'   => 'South Asia',
                'country_codes'  => ['PK', 'BD', 'LK', 'NP', 'AF'],
                'price_monthly'  => 3.99,
                'price_yearly'   => 38.31,
                'currency_code'  => 'USD',
                'stripe_price_id_monthly' => null,
                'stripe_price_id_yearly'  => null,
                'is_active'      => true,
            ],
            [
                'region_code'    => 'INDIA',
                'region_label'   => 'India',
                'country_codes'  => ['IN'],
                'price_monthly'  => 4.99,
                'price_yearly'   => 47.90,
                'currency_code'  => 'USD',
                'stripe_price_id_monthly' => null,
                'stripe_price_id_yearly'  => null,
                'is_active'      => true,
            ],
            [
                'region_code'    => 'SEA',
                'region_label'   => 'Southeast Asia',
                'country_codes'  => ['ID', 'MY', 'PH', 'VN', 'TH', 'MM', 'KH', 'LA'],
                'price_monthly'  => 5.99,
                'price_yearly'   => 57.50,
                'currency_code'  => 'USD',
                'stripe_price_id_monthly' => null,
                'stripe_price_id_yearly'  => null,
                'is_active'      => true,
            ],
            [
                'region_code'    => 'MENA',
                'region_label'   => 'Middle East & North Africa',
                'country_codes'  => ['SA', 'AE', 'QA', 'KW', 'BH', 'OM', 'JO', 'LB', 'IQ', 'SY', 'YE', 'EG', 'MA', 'TN', 'DZ'],
                'price_monthly'  => 14.99,
                'price_yearly'   => 143.90,
                'currency_code'  => 'USD',
                'stripe_price_id_monthly' => null,
                'stripe_price_id_yearly'  => null,
                'is_active'      => true,
            ],
            [
                'region_code'    => 'EUROPE',
                'region_label'   => 'Europe',
                'country_codes'  => ['GB', 'DE', 'FR', 'NL', 'BE', 'IT', 'ES', 'SE', 'NO', 'DK', 'FI', 'CH', 'AT', 'PT', 'IE', 'PL', 'CZ', 'HU'],
                'price_monthly'  => 9.99,
                'price_yearly'   => 95.90,
                'currency_code'  => 'USD',
                'stripe_price_id_monthly' => null,
                'stripe_price_id_yearly'  => null,
                'is_active'      => true,
            ],
            [
                'region_code'    => 'GLOBAL',
                'region_label'   => 'Global',
                'country_codes'  => [],
                'price_monthly'  => 12.99,
                'price_yearly'   => 124.70,
                'currency_code'  => 'USD',
                'stripe_price_id_monthly' => null,
                'stripe_price_id_yearly'  => null,
                'is_active'      => true,
            ],
        ];

        foreach ($tiers as $tier) {
            PricingTier::updateOrCreate(
                ['region_code' => $tier['region_code']],
                $tier
            );
        }
    }
}

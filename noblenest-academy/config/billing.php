<?php

/**
 * Phase 4 — canonical product + price definitions.
 *
 * Read by `php artisan stripe:sync-prices`, which creates / updates Stripe
 * Products + Prices and writes the resulting IDs back to PricingTier rows.
 *
 * Server-side price resolution (PaymentController::stripeCheckout) looks up
 * the right `price_id` based on (plan, region) — the client never supplies
 * an amount. Hidden-amount forms are gone.
 *
 * Region mapping: PricingTier.country_codes drives which region a request
 * resolves to. The default region is `GLOBAL` (≈ US pricing).
 *
 * Trial: every paid plan ships with a 7-day free trial on first purchase.
 */
return [
    'currency_default' => env('BILLING_CURRENCY', 'USD'),
    'trial_days'       => (int) env('BILLING_TRIAL_DAYS', 7),

    // automatic_tax flag passed through to Stripe Checkout Session.
    'tax_enabled'      => (bool) env('STRIPE_TAX_ENABLED', true),

    'products' => [
        'individual' => [
            'name'        => 'Noble Nest — Individual',
            'description' => 'Unlimited curriculum + STEM access for one child.',
            'metadata'    => ['tier' => 'individual'],
        ],
        'family' => [
            'name'        => 'Noble Nest — Family',
            'description' => 'Unlimited access for up to 4 children + family dashboard.',
            'metadata'    => ['tier' => 'family'],
        ],
        'school' => [
            'name'        => 'Noble Nest — School / Center',
            'description' => 'Per-seat institutional licence with SSO and analytics.',
            'metadata'    => ['tier' => 'school'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Customer Portal
    |--------------------------------------------------------------------------
    |
    | Stripe's hosted portal lets customers cancel, upgrade, switch plans,
    | update card, and download invoices. Configure the portal in the Stripe
    | dashboard (settings → billing → customer portal), then set the public
    | return URL here.
    */
    'portal' => [
        'return_url' => env('STRIPE_PORTAL_RETURN_URL', '/'),
    ],
];

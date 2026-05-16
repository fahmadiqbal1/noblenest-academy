<?php

namespace App\Console\Commands;

use App\Models\PricingTier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Phase 4 — sync Stripe Products + Prices from config to the live account.
 *
 * Usage:
 *   php artisan stripe:sync-prices              # apply
 *   php artisan stripe:sync-prices --dry-run    # plan only
 *
 * Walks every active PricingTier row, ensures a Stripe Product exists per
 * tier × plan-type ("individual monthly", "individual yearly", etc.), then
 * creates / updates a Stripe Price (in the row's `currency_code`) and writes
 * the resulting price IDs back into `pricing_tiers.stripe_price_id_monthly`
 * / `_yearly`.
 *
 * Idempotent: Stripe Products + Prices are looked up by metadata.tier and
 * metadata.region; existing rows are reused. New prices are created when
 * the amount changes (Stripe doesn't allow Price.update on `unit_amount`).
 */
class StripeSyncPricesCommand extends Command
{
    protected $signature = 'stripe:sync-prices {--dry-run : Preview without writing}';
    protected $description = 'Create / update Stripe Products + Prices to match config/billing.php and pricing_tiers.';

    public function handle(): int
    {
        if (! class_exists(\Stripe\Stripe::class)) {
            $this->error('Stripe SDK not installed. Run `composer require stripe/stripe-php`.');
            return self::FAILURE;
        }
        $secret = config('services.stripe.secret');
        if (empty($secret)) {
            $this->error('STRIPE_SECRET_KEY not configured.');
            return self::FAILURE;
        }
        \Stripe\Stripe::setApiKey($secret);

        $dry = (bool) $this->option('dry-run');
        $tiers = PricingTier::query()->where('is_active', true)->orderBy('region_code')->get();
        if ($tiers->isEmpty()) {
            $this->warn('No active pricing tiers. Seed them first.');
            return self::SUCCESS;
        }

        $createdProducts = 0; $createdPrices = 0; $updatedTiers = 0;

        // One Product per logical tier (individual/family/school); region drives Price only.
        $logicalTier = 'individual';   // every PricingTier row maps to "individual" for now;
                                       // family/school live in a different table or column once
                                       // multi-tier support lands.
        $productConfig = config("billing.products.{$logicalTier}");
        if (! $productConfig) {
            $this->error("billing.products.{$logicalTier} not configured.");
            return self::FAILURE;
        }

        $product = $this->ensureProduct($logicalTier, $productConfig, $dry);
        if ($product) {
            $createdProducts = (int) ($product->_created ?? 0);
        }

        foreach ($tiers as $tier) {
            $this->line("→ {$tier->region_label} ({$tier->region_code})  {$tier->price_monthly}/{$tier->price_yearly} {$tier->currency_code}");

            $monthly = $this->ensurePrice($product, $tier, 'month', $dry);
            $yearly  = $this->ensurePrice($product, $tier, 'year',  $dry);

            if (! $dry) {
                $tier->stripe_price_id_monthly = $monthly->id ?? $tier->stripe_price_id_monthly;
                $tier->stripe_price_id_yearly  = $yearly->id  ?? $tier->stripe_price_id_yearly;
                if ($tier->isDirty()) {
                    $tier->save();
                    $updatedTiers++;
                }
            }
            if (($monthly->_created ?? 0)) $createdPrices++;
            if (($yearly->_created ?? 0))  $createdPrices++;
        }

        $this->newLine();
        $this->info(sprintf(
            '%s: %d product(s) created, %d price(s) created, %d tier row(s) updated.',
            $dry ? 'Dry-run' : 'Done',
            $createdProducts, $createdPrices, $updatedTiers
        ));
        return self::SUCCESS;
    }

    private function ensureProduct(string $tierKey, array $config, bool $dry)
    {
        $existing = \Stripe\Product::all(['limit' => 100])->data;
        foreach ($existing as $p) {
            if (($p->metadata->tier ?? null) === $tierKey) {
                return $p;
            }
        }
        if ($dry) {
            $this->line("  (dry) would create Product: {$config['name']}");
            return (object) ['id' => "prod_pretend_{$tierKey}", '_created' => 1];
        }
        $product = \Stripe\Product::create([
            'name'        => $config['name'],
            'description' => $config['description'],
            'metadata'    => array_merge(['tier' => $tierKey], $config['metadata'] ?? []),
        ]);
        $product->_created = 1;
        $this->info("  created Product: {$product->id} ({$config['name']})");
        return $product;
    }

    private function ensurePrice($product, PricingTier $tier, string $interval, bool $dry)
    {
        $amountCents = (int) round((($interval === 'month' ? $tier->price_monthly : $tier->price_yearly)) * 100);
        $currency    = strtolower($tier->currency_code);
        $lookupKey   = sprintf('nn_%s_%s_%s_%s', $tier->region_code, $interval, $currency, $amountCents);

        // Try to reuse an existing Price matching this lookup key.
        try {
            $existing = \Stripe\Price::all([
                'product'    => $product->id,
                'lookup_keys' => [$lookupKey],
                'limit'      => 1,
            ]);
            if (! empty($existing->data)) {
                return $existing->data[0];
            }
        } catch (\Throwable $e) {
            // fall through to create
        }

        if ($dry) {
            $this->line("  (dry) would create Price: {$lookupKey} = {$amountCents} {$currency} / {$interval}");
            return (object) ['id' => "price_pretend_{$lookupKey}", '_created' => 1];
        }

        $price = \Stripe\Price::create([
            'product'     => $product->id,
            'currency'    => $currency,
            'unit_amount' => $amountCents,
            'recurring'   => ['interval' => $interval],
            'lookup_key'  => $lookupKey,
            'metadata'    => [
                'region' => $tier->region_code,
                'tier'   => 'individual',
                'plan'   => $interval === 'month' ? 'monthly' : 'yearly',
            ],
        ]);
        $price->_created = 1;
        $this->info("  created Price: {$price->id}  {$amountCents} {$currency} / {$interval}");
        return $price;
    }
}

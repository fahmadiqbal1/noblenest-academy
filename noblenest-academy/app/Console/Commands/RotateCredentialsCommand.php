<?php

namespace App\Console\Commands;

use App\Services\SecureCredentialsManager;
use Illuminate\Console\Command;

/**
 * Rotate API credentials for improved security
 *
 * Usage:
 *   php artisan credentials:rotate openai api_key sk-newkey123
 *   php artisan credentials:rotate stripe secret_key sk_live_newkey
 */
class RotateCredentialsCommand extends Command
{
    protected $signature = 'credentials:rotate {provider} {key} {value}';
    protected $description = 'Rotate an API credential to a new value';

    public function handle(SecureCredentialsManager $manager): int
    {
        $provider = $this->argument('provider');
        $key = $this->argument('key');
        $value = $this->argument('value');

        $this->info("Rotating credential: {$provider}/{$key}");

        if ($manager->storeCredential($provider, $key, $value, auth()->user()?->email ?? 'CLI')) {
            $this->info("✓ Credential rotated successfully");
            $this->line("  Provider: {$provider}");
            $this->line("  Key: {$key}");
            $this->line("  Rotated at: " . now()->toDateTimeString());
            return self::SUCCESS;
        }

        $this->error("✗ Failed to rotate credential");
        return self::FAILURE;
    }
}

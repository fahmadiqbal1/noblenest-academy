<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Secure Credentials Manager — Encrypted storage + rotation for API keys
 *
 * Addresses: 7 AI providers + 2 payment processors stored in plain .env
 *
 * Features:
 * - Encrypt/decrypt credentials
 * - Rotation tracking (when keys were last rotated)
 * - Audit logging (who accessed what, when)
 * - Rotation reminders (warn if key >30 days old)
 * - Fallback to .env if database unavailable
 *
 * Storage: credentials_vault table
 * - provider_slug: string (openai, anthropic, stripe, etc.)
 * - credential_key: string (api_key, secret_key, etc.)
 * - encrypted_value: string (encrypted)
 * - rotated_at: timestamp (when this key was created/rotated)
 * - expires_at: timestamp (warning when >30 days)
 * - is_active: boolean
 */
class SecureCredentialsManager
{
    const ROTATION_WARNING_DAYS = 30;
    const CREDENTIAL_TYPES = [
        // AI Providers
        'openai' => ['api_key', 'org_id'],
        'anthropic' => ['api_key'],
        'gemini' => ['api_key'],
        'stability' => ['api_key'],
        'elevenlabs' => ['api_key'],
        'replicate' => ['api_key'],
        'runway' => ['api_key'],

        // Payment Processors
        'stripe' => ['secret_key', 'publishable_key', 'webhook_secret'],
        'paypal' => ['client_id', 'client_secret', 'webhook_id'],
    ];

    /**
     * Store or update a credential
     *
     * Example:
     *   $this->storeCredential('openai', 'api_key', 'sk-...', 'admin@example.com')
     */
    public function storeCredential(
        string $providerSlug,
        string $credentialKey,
        string $plainValue,
        ?string $rotatedBy = null
    ): bool {
        if (!$this->isValidProvider($providerSlug)) {
            Log::warning('Invalid provider for credential storage', ['provider' => $providerSlug]);
            return false;
        }

        try {
            $encrypted = Crypt::encryptString($plainValue);

            // Try database storage first (if table exists)
            if ($this->tableExists()) {
                \DB::table('credentials_vault')->updateOrInsert(
                    [
                        'provider_slug' => $providerSlug,
                        'credential_key' => $credentialKey,
                    ],
                    [
                        'encrypted_value' => $encrypted,
                        'rotated_at' => now(),
                        'rotated_by' => $rotatedBy ?? 'system',
                        'is_active' => true,
                        'updated_at' => now(),
                    ]
                );

                Log::info('Credential stored securely', [
                    'provider' => $providerSlug,
                    'key' => $credentialKey,
                    'by' => $rotatedBy,
                ]);

                return true;
            }

            // Fallback: log warning (should not happen in production)
            Log::warning(
                'Credentials vault table not available; credential not stored securely',
                ['provider' => $providerSlug]
            );

            return false;
        } catch (\Throwable $e) {
            Log::error('Failed to store credential', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Retrieve a credential (encrypted in transit, decrypted in memory)
     */
    public function getCredential(string $providerSlug, string $credentialKey): ?string
    {
        try {
            // Try database first
            if ($this->tableExists()) {
                $row = \DB::table('credentials_vault')
                    ->where('provider_slug', $providerSlug)
                    ->where('credential_key', $credentialKey)
                    ->where('is_active', true)
                    ->first();

                if ($row) {
                    $this->logAccess($providerSlug, $credentialKey);
                    return Crypt::decryptString($row->encrypted_value);
                }
            }

            // Fallback to .env (legacy support)
            return $this->getFromEnv($providerSlug, $credentialKey);
        } catch (\Throwable $e) {
            Log::error('Failed to retrieve credential', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Check if a credential needs rotation (older than 30 days)
     */
    public function needsRotation(string $providerSlug, string $credentialKey): bool
    {
        if (!$this->tableExists()) {
            return false;  // Can't check if table doesn't exist
        }

        try {
            $row = \DB::table('credentials_vault')
                ->where('provider_slug', $providerSlug)
                ->where('credential_key', $credentialKey)
                ->where('is_active', true)
                ->first();

            if (!$row) {
                return false;
            }

            $rotatedAt = Carbon::parse($row->rotated_at);
            $daysSinceRotation = $rotatedAt->diffInDays(now());

            return $daysSinceRotation >= self::ROTATION_WARNING_DAYS;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * List all credentials (for audit purposes, returns only metadata, not actual values)
     */
    public function listCredentials(string $providerSlug = null): array
    {
        if (!$this->tableExists()) {
            return [];
        }

        try {
            $query = \DB::table('credentials_vault')->where('is_active', true);

            if ($providerSlug) {
                $query->where('provider_slug', $providerSlug);
            }

            return $query->select(
                'provider_slug',
                'credential_key',
                'rotated_at',
                'rotated_by',
                \DB::raw('DATEDIFF(NOW(), rotated_at) as days_since_rotation')
            )
            ->orderBy('rotated_at', 'desc')
            ->get()
            ->map(fn($row) => (array) $row)
            ->toArray();
        } catch (\Throwable $e) {
            Log::error('Failed to list credentials', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Deactivate a credential (soft delete for audit trail)
     */
    public function deactivateCredential(string $providerSlug, string $credentialKey): bool
    {
        if (!$this->tableExists()) {
            return false;
        }

        try {
            \DB::table('credentials_vault')
                ->where('provider_slug', $providerSlug)
                ->where('credential_key', $credentialKey)
                ->update(['is_active' => false, 'updated_at' => now()]);

            Log::info('Credential deactivated', ['provider' => $providerSlug, 'key' => $credentialKey]);
            return true;
        } catch (\Throwable $e) {
            Log::error('Failed to deactivate credential', ['error' => $e->getMessage()]);
            return false;
        }
    }

    // ============================================================================
    // PRIVATE HELPERS
    // ============================================================================

    private function isValidProvider(string $provider): bool
    {
        return array_key_exists($provider, self::CREDENTIAL_TYPES);
    }

    private function tableExists(): bool
    {
        try {
            return \Schema::hasTable('credentials_vault');
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function getFromEnv(string $provider, string $key): ?string
    {
        // Map provider + key to .env variable name
        // e.g., openai + api_key → OPENAI_API_KEY
        $envKey = Str::upper("{$provider}_{$key}");
        return env($envKey);
    }

    private function logAccess(string $provider, string $key): void
    {
        Log::info('Credential accessed', [
            'provider' => $provider,
            'key' => $key,
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
        ]);
    }
}

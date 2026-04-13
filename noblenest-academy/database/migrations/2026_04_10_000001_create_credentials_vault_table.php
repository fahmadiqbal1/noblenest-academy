<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('credentials_vault', function (Blueprint $table) {
            $table->id();

            // Credential identification
            $table->string('provider_slug')->index();  // openai, stripe, etc.
            $table->string('credential_key');          // api_key, secret_key, etc.

            // Encrypted value
            $table->longText('encrypted_value');

            // Rotation tracking
            $table->timestamp('rotated_at')->index();  // When created/rotated
            $table->string('rotated_by')->default('system');  // Who rotated it

            // Status
            $table->boolean('is_active')->default(true)->index();

            // Timestamps
            $table->timestamps();

            // Unique constraint: only one active credential per provider+key
            $table->unique(['provider_slug', 'credential_key'], 'unique_active_credential')
                  ->where('is_active', true);
        });

        // Log table for audit trail
        Schema::create('credential_audit_logs', function (Blueprint $table) {
            $table->id();

            $table->string('provider_slug');
            $table->string('credential_key');
            $table->enum('action', ['created', 'accessed', 'rotated', 'deactivated']);
            $table->string('user_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['provider_slug', 'action', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credential_audit_logs');
        Schema::dropIfExists('credentials_vault');
    }
};

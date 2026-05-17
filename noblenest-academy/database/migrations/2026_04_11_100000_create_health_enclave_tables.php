<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ===== HEALTH ENCLAVE SCHEMA (separate connection) =====
        // These tables ONLY accessible via HealthEnclaveService
        // No foreign keys back to main app — only signed tokens for reference

        Schema::create('parent_consents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_user_id');
            $table->unsignedBigInteger('child_profile_id');
            $table->string('data_source');
            // MySQL 8 forbids a DEFAULT on JSON columns (err 1101); MariaDB
            // permits it. Keep portable: nullable, default handled by the
            // model cast / application layer.
            $table->json('scope')->nullable();
            $table->timestamp('granted_at')->useCurrent();
            $table->timestamp('revoked_at')->nullable();
            $table->unsignedSmallInteger('version')->default(1);
            $table->string('signed_hash', 255);
            $table->timestamps();

            $table->unique(['parent_user_id', 'child_profile_id', 'data_source'], 'pc_parent_child_source_unique');
            $table->index('revoked_at');
        });

        Schema::create('health_data_ingestions', function (Blueprint $table) {
            $table->id();
            $table->json('raw_payload');
            $table->string('source', 100);
            $table->timestamp('received_at')->useCurrent();
            $table->string('integrity_hash', 255);
            $table->unsignedBigInteger('consent_id');
            $table->foreign('consent_id')->references('id')->on('parent_consents')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['consent_id', 'received_at']);
        });

        Schema::create('health_data_facts', function (Blueprint $table) {
            $table->id();
            $table->string('fact_type', 100);
            $table->string('fact_value', 255);
            $table->string('source', 100);
            $table->unsignedBigInteger('child_profile_id');
            $table->unsignedBigInteger('consent_id');
            $table->foreign('consent_id')->references('id')->on('parent_consents')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['child_profile_id', 'fact_type']);
            $table->index('created_at');
        });

        Schema::create('health_enclave_access_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('actor_user_id')->nullable();
            $table->string('purpose', 255);
            $table->unsignedBigInteger('consent_id');
            $table->timestamp('accessed_at')->useCurrent();
            $table->foreign('consent_id')->references('id')->on('parent_consents')->cascadeOnDelete();

            $table->index(['consent_id', 'accessed_at']);
            $table->index('purpose');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_enclave_access_logs');
        Schema::dropIfExists('health_data_facts');
        Schema::dropIfExists('health_data_ingestions');
        Schema::dropIfExists('parent_consents');
    }
};

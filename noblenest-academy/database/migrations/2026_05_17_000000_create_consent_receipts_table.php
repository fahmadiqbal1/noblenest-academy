<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consent_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('child_profile_id')->constrained('child_profiles')->cascadeOnDelete();
            $table->string('document_version', 32);
            $table->string('ip', 45);
            $table->string('user_agent', 512);
            $table->timestamp('signed_at');
            $table->timestamp('withdrawn_at')->nullable();
            $table->timestamps();

            $table->index(['child_profile_id', 'document_version'], 'cr_child_doc_idx');
            $table->index(['parent_user_id', 'signed_at'], 'cr_parent_signed_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consent_receipts');
    }
};

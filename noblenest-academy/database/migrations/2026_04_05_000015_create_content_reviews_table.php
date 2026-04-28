<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practitioner_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('maternal_content_id')->constrained()->cascadeOnDelete();

            $table->string('decision', 15);            // approved | rejected | flagged
            $table->text('side_notes')->nullable();     // Visible to parents as warnings
            $table->text('internal_notes')->nullable(); // Admin-only notes
            $table->string('credential_used');          // Snapshot of license_type at review time
            $table->string('credential_number');        // Snapshot of license_number at review time
            $table->timestamp('reviewed_at');

            $table->timestamps();

            $table->unique(['practitioner_profile_id', 'maternal_content_id'], 'unique_practitioner_content_review');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_reviews');
    }
};

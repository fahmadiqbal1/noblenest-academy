<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practitioner_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            $table->text('license_number');                   // Encrypted at app layer
            $table->string('license_type', 40);               // medical_doctor, midwife, etc.
            $table->string('credential_body');                 // Issuing organisation
            $table->string('specialization');                  // Area of expertise
            $table->string('certificate_path')->nullable();    // Uploaded file (private disk)
            $table->text('bio')->nullable();
            $table->unsignedSmallInteger('years_experience')->default(0);

            $table->string('verification_status', 15)->default('active'); // active | suspended
            $table->text('suspended_reason')->nullable();
            $table->unsignedInteger('verified_content_count')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practitioner_profiles');
    }
};

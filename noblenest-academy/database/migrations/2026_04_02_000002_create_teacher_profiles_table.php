<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('headline', 255)->nullable();
            $table->text('bio')->nullable();
            $table->string('country', 100)->nullable();
            $table->string('timezone', 100)->nullable();
            $table->string('languages_spoken', 255)->nullable(); // comma-separated
            $table->json('credentials')->nullable(); // [{degree, institution, year}]
            $table->string('cv_path')->nullable(); // S3 path
            $table->enum('status', ['pending', 'approved', 'rejected', 'auto_approved'])->default('pending');
            $table->string('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->boolean('is_edu_email')->default(false); // auto-approve trigger
            $table->unsignedInteger('lesson_count')->default(0);
            $table->decimal('rating_average', 3, 2)->default(0.00);
            $table->unsignedInteger('rating_count')->default(0);
            $table->decimal('payout_rate', 5, 2)->default(0.70); // 70% default revenue share
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_profiles');
    }
};

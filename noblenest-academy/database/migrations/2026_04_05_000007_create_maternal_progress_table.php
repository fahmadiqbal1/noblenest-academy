<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maternal_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maternal_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('maternal_content_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('not_started');
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('time_spent')->default(0);
            $table->unsignedTinyInteger('rating')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['maternal_profile_id', 'maternal_content_id'], 'maternal_progress_unique');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maternal_progress');
    }
};

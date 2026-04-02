<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('share_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('activity_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('badge_id')->nullable()->constrained()->nullOnDelete();
            $table->string('card_type', 50)->default('activity_complete'); // activity_complete|badge|streak|milestone
            $table->string('image_url')->nullable(); // S3 URL of generated PNG
            $table->unsignedInteger('share_count')->default(0);
            $table->timestamps();

            $table->index(['child_profile_id', 'card_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('share_cards');
    }
};

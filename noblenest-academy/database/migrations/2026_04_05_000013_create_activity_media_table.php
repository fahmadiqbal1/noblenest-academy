<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained()->cascadeOnDelete();
            $table->string('media_type', 20);
            $table->string('url');
            $table->string('label')->nullable();
            $table->string('modality', 15);
            $table->unsignedSmallInteger('order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->timestamps();

            $table->index(['activity_id', 'media_type']);
            $table->index(['activity_id', 'modality']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_media');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maternal_content_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maternal_content_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('step_number');
            $table->string('title');
            $table->text('instruction');
            $table->string('visual_url')->nullable();
            $table->string('video_url')->nullable();
            $table->string('audio_url')->nullable();
            $table->unsignedSmallInteger('duration_seconds')->nullable();
            $table->text('tip')->nullable();
            $table->timestamps();

            $table->index(['maternal_content_id', 'step_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maternal_content_steps');
    }
};

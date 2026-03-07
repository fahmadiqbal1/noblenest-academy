<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('type');                         // e.g. video_lesson, translation, image, tts
            $table->string('status')->default('queued');    // queued|running|completed|failed
            $table->string('provider')->nullable();         // openai|anthropic|gemini|mock
            $table->string('locale', 8)->default('en');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->json('payload')->nullable();            // input data / prompt
            $table->json('result')->nullable();             // generated output reference
            $table->string('moderation_status')->default('pending'); // pending|approved|rejected
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('type');
            $table->index('moderation_status');
        });

        Schema::create('ai_provider_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();               // display name
            $table->string('slug')->unique();               // e.g. openai, anthropic, github
            $table->string('api_base_url')->nullable();
            $table->text('api_key_encrypted')->nullable();  // store encrypted
            $table->string('model')->nullable();            // default model
            $table->boolean('is_active')->default(true);
            $table->json('capabilities')->nullable();       // e.g. ['text','image','tts']
            $table->json('extra_config')->nullable();       // repo_url, headers, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_provider_configs');
        Schema::dropIfExists('ai_jobs');
    }
};

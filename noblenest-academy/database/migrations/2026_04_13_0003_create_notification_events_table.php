<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->enum('channel', ['database', 'mail', 'push', 'sms']);
            $table->string('variant_key')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type', 'sent_at']);
            $table->index('sent_at');
            $table->index(['variant_key', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_events');
    }
};

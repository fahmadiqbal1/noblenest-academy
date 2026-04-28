<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('share_card_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('share_card_id')->constrained('share_cards')->cascadeOnDelete();
            $table->string('token', 32)->unique();
            $table->foreignId('shared_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('platform', ['whatsapp', 'instagram', 'facebook', 'x', 'copy', 'other']);
            $table->unsignedInteger('viewed_count')->default(0);
            $table->unsignedInteger('clicked_register_count')->default(0);
            $table->unsignedInteger('attributed_signup_count')->default(0);
            $table->timestamps();

            $table->index('token');
            $table->index('share_card_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('share_card_shares');
    }
};

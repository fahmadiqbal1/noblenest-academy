<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_variants', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('variant_key');
            $table->tinyInteger('weight')->default(50);
            $table->text('subject_template')->nullable();
            $table->text('body_template')->nullable();
            $table->text('push_title_template')->nullable();
            $table->text('push_body_template')->nullable();
            $table->boolean('enabled')->default(true);
            $table->timestamps();

            $table->unique(['type', 'variant_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_variants');
    }
};

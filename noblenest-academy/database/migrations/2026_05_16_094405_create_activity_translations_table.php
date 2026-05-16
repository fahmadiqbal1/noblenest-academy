<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 3 — activity translations.
 *
 * EAV-style table per the master prompt's `activity_translations(activity_id,
 * locale, field, value)` shape. One canonical Activity row stays in English;
 * per-locale overrides for any translatable field live here. The AI batch
 * translation pipeline writes rows in bulk via `Activity::translation()`.
 *
 * Supported locales: en, fr, ru, zh, es, ko, ur, ar. ur + ar are RTL.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')
                  ->constrained('activities')
                  ->cascadeOnDelete();
            $table->string('locale', 8);                  // e.g. 'en', 'ar', 'zh-Hans'
            $table->string('field', 64);                  // e.g. 'title', 'description', 'instructions_for_parent'
            $table->text('value');
            $table->timestamps();

            // One row per (activity, locale, field). Re-translation overwrites.
            $table->unique(['activity_id', 'locale', 'field'], 'act_trans_unique');
            // Lookup by locale + field for "give me every translated title in Arabic".
            $table->index(['locale', 'field'], 'act_trans_locale_field');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_translations');
    }
};

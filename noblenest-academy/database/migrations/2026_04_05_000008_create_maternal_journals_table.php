<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maternal_journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maternal_profile_id')->constrained()->cascadeOnDelete();
            $table->date('entry_date');
            $table->unsignedTinyInteger('week_number');
            $table->string('mood', 20)->nullable();
            $table->unsignedTinyInteger('energy_level')->nullable();
            $table->text('symptoms_encrypted')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedSmallInteger('baby_kicks_count')->nullable();
            $table->timestamps();

            $table->unique(['maternal_profile_id', 'entry_date']);
            $table->index('entry_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maternal_journals');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maternal_emergency_signs', function (Blueprint $table) {
            $table->id();
            $table->string('stage', 30);
            $table->string('symptom');
            $table->string('severity', 15);
            $table->text('action_text');
            $table->unsignedSmallInteger('order')->default(0);
            $table->string('language', 8)->default('en');
            $table->timestamps();

            $table->index(['stage', 'language']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maternal_emergency_signs');
    }
};

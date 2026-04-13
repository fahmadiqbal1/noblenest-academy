<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contraindication_matrix', function (Blueprint $table) {
            $table->id();
            $table->string('condition', 60);
            $table->foreignId('maternal_content_id')->constrained()->cascadeOnDelete();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->unique(['condition', 'maternal_content_id'], 'contra_matrix_unique');
            $table->index('condition');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contraindication_matrix');
    }
};

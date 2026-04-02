<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('school_name', 255);
            $table->string('contact_name', 150);
            $table->string('contact_email', 255);
            $table->string('contact_phone', 50)->nullable();
            $table->string('country', 100)->nullable();
            $table->unsignedSmallInteger('student_count')->nullable();
            $table->text('message')->nullable();
            $table->enum('status', ['new', 'contacted', 'demo_scheduled', 'converted', 'declined'])->default('new');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_inquiries');
    }
};

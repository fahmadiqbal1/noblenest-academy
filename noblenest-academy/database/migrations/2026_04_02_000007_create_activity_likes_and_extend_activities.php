<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['activity_id', 'user_id']);
        });

        // Add is_free flag and like_count cache to activities
        Schema::table('activities', function (Blueprint $table) {
            $table->boolean('is_free')->default(false)->after('is_rtl');
            $table->unsignedInteger('like_count')->default(0)->after('is_free');
            $table->string('age_tier', 20)->nullable()->after('like_count'); // baby|toddler|preschool|school
            $table->string('emoji', 10)->nullable()->after('age_tier');
            $table->string('subject', 50)->nullable()->after('emoji');
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn(['is_free', 'like_count', 'age_tier', 'emoji', 'subject']);
        });
        Schema::dropIfExists('activity_likes');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('last_dashboard_view_at')->nullable();
            $table->unsignedTinyInteger('engagement_score')->default(0);
            $table->timestamp('inactivity_alert_sent_at')->nullable();
            $table->string('phone_e164', 20)->nullable();
            $table->boolean('sms_opt_in')->default(false);
            $table->timestamp('sms_opt_in_at')->nullable();

            $table->index('phone_e164');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['phone_e164']);
            $table->dropColumn([
                'last_login_at',
                'last_dashboard_view_at',
                'engagement_score',
                'inactivity_alert_sent_at',
                'phone_e164',
                'sms_opt_in',
                'sms_opt_in_at',
            ]);
        });
    }
};

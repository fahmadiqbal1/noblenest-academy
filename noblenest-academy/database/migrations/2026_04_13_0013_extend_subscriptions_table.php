<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('stripe_subscription_id')->nullable()->unique();
            $table->string('stripe_customer_id')->nullable();
            $table->enum('status', ['trialing', 'active', 'past_due', 'canceled', 'incomplete'])->default('active');
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('cancel_at')->nullable();

            $table->index('stripe_customer_id');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropUnique(['stripe_subscription_id']);
            $table->dropIndex(['stripe_customer_id']);
            $table->dropColumn([
                'stripe_subscription_id',
                'stripe_customer_id',
                'status',
                'current_period_end',
                'cancel_at',
            ]);
        });
    }
};

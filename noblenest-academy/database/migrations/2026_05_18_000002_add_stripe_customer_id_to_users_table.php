<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds users.stripe_customer_id (C6).
 *
 * PaymentController reads/writes $user->stripe_customer_id in 5 places
 * (stripeCheckout, handleCheckoutCompleted, handleCustomerUpdated) but the
 * column only ever existed on `subscriptions`. Every real Stripe
 * checkout.session.completed webhook therefore threw "no such column" and
 * was swallowed as a 500 — no subscription was ever created from a live
 * payment. Indexed because handleCustomerUpdated looks users up by it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'stripe_customer_id')) {
                $table->string('stripe_customer_id')->nullable()->index()->after('email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'stripe_customer_id')) {
                $table->dropColumn('stripe_customer_id');
            }
        });
    }
};

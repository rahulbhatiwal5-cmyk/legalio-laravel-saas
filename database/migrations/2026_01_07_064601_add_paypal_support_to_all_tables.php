<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('paypal_plan_id')->nullable()->after('stripe_product_id');
            $table->string('paypal_product_id')->nullable()->after('paypal_plan_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('paypal_payer_id')->nullable()->index()->after('stripe_cus_id');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('paypal_sale_id')->nullable()->after('stripe_customer_id');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('paypal_subscription_id')->nullable()->after('stripe_subscription_id');
            $table->string('paypal_plan_id')->nullable()->after('paypal_subscription_id');
            $table->string('paypal_status')->nullable()->after('stripe_status');
            $table->string('payment_gateway')->nullable()->after('paypal_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['paypal_subscription_id', 'paypal_plan_id', 'paypal_status', 'payment_gateway']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('paypal_sale_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('paypal_payer_id');
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['paypal_plan_id', 'paypal_product_id']);
        });
    }
};

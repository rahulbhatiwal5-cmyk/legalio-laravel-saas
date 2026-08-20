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
        Schema::create('orders', function (Blueprint $table) {
            $table->id()->startingValue(1000);
            $table->string('order_num')->nullable();
            $table->string('paypal_order_id')->nullable();
            $table->integer('document_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('amount')->nullable();
            $table->integer('discount_amount')->nullable();
            $table->integer('total_amount')->nullable();
            $table->string('stripe_subscription_id')->nullable();
            $table->enum('order_type',['one_time','subscription'])->default('one_time');
            $table->integer('status')->default(0)->comment('0: Pending, 1: Succeeded, 2: Refunded, 3: Cancelled, 4: Expired');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

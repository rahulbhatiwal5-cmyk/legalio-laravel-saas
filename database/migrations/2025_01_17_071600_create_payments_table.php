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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->nullable();
            $table->string('payment_intent')->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->integer('amount')->nullable();
            $table->integer('discount')->nullable();
            $table->integer('total_amount')->nullable();
            $table->string('type')->nullable();
            $table->enum('pay_type',['one_time','subscription'])->default('one_time');
            $table->text('description')->nullable();
            $table->string('discount_code')->nullable();
            $table->string('is_discount_applied')->nullable();
            $table->string('status')->default('incomplete');
            $table->string('payment_type')->nullable();
            

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

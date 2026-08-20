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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('stripe_subscription_id')->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->enum('status', ['active', 'cancel', 'incomplete', 'completed', 'paused', 'trial_paused','trialing'])->default('incomplete');
            $table->string('stripe_status')->nullable();
            $table->boolean('is_paused')->default(false);
            $table->timestamp('pause_start_at')->nullable();
            $table->timestamp('pause_end_at')->nullable();
            $table->string('pause_behavior')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamp('current_period_start_date')->nullable();
            $table->timestamp('current_period_end_date')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('set null');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};

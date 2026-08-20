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
        Schema::create('free_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->integer('grant_access_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('order_id')->nullable(); 
            $table->integer('plan_id')->nullable();
            $table->string('subscription_id')->nullable(); 
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->string('status')->default('active'); 
            $table->string('type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('free_subscriptions');
    }
};

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
        Schema::create('subscription_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('event_type'); 
            $table->text('description')->nullable();
            $table->json('payload')->nullable();

            $table->enum('status', ['active', 'cancel', 'incomplete', 'completed'])->nullable();
            $table->timestamps();

            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
         
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_logs');
    }
};

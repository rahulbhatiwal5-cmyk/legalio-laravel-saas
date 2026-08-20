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
        Schema::create('credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade')->nullable();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('subscription_id')->nullable();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->integer('interval')->nullable();
            $table->integer('carry_forward')->nullable();
            $table->integer('used_amount')->nullable();
            $table->date('purchase_date')->nullable();
            $table->integer('type')->default(0)->comment('0: Out, 1: In');
            $table->integer('amount')->nullable();
            $table->date('transaction_date')->nullable();
            $table->longText('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_transactions');
    }
};

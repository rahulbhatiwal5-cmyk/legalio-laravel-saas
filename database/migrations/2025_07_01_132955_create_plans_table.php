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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->enum('interval', ['monthly', 'yearly'])->default('monthly');
            $table->decimal('price', 10, 2)->nullable();
            $table->string('currency')->default('USD');
            $table->integer('credit')->default(0);
            $table->integer('number_of_months')->nullable();
            $table->text('stripe_price_id')->nullable();
            $table->text('stripe_product_id')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('allowed_users')->default(1);
            $table->integer('trial_days')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('access_documents')->default(1)->comment('1: has_all_documents; 2: has_excluded_documents; 3: has_included_documents');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};

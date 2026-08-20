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
        Schema::create('sub_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_condition_id')->constrained()->onDelete('cascade');
            $table->integer('key')->nullable(); // Key for the condition
            $table->unsignedBigInteger('conditional_question_id')->nullable(); // Question ID to check condition on
            $table->text('conditional_question_value')->nullable(); // Value to check against for the condition
            $table->integer('conditional_check')->comment('1: is equal to; 2: is greater than; 3: is less than; 4: not equal to');
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->foreign('conditional_question_id')->references('id')->on('questions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_conditions');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 
     * 
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('multiple_choice_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade'); // Foreign key to questions table
            $table->string('option_label')->nullable(); // Label of the option
            $table->string('option_value')->nullable(); // Value of the option
            $table->unsignedBigInteger('next_question_id')->nullable(); // Optional next question ID
            $table->text('contract_link')->nullable();
            $table->boolean('contract_send_to_next_step')->default(false);
            $table->string('type')->nullable(); // Type of question
            $table->integer('order_id')->nullable(); // Order ID for ordering options
            $table->timestamps();

            // Foreign key for next_question_id
            // $table->foreign('next_question_id')->references('id')->on('questions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('multiple_choice_question_options');
    }
};

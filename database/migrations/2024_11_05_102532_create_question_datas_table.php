<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('question_datas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade'); // Foreign key to questions table
            $table->text('question_label')->nullable(); // Label of the question
            $table->unsignedBigInteger('textbox_id')->nullable(); // Optional textbox ID
            $table->unsignedBigInteger('next_question_id')->nullable(); // Optional next question ID
            $table->text('same_contract_link_label')->nullable();
            // $table->foreign('next_question_id')->references('id')->on('questions')->onDelete('set null');
            $table->integer('conditional_go_to_step')->nullable();
            $table->longText('text_box_placeholder')->nullable(); // Placeholder text for textbox questions
            $table->longText('question_info_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_datas');
    }
};

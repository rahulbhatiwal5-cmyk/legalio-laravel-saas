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
        Schema::create('global_contract_question_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('global_contract_questions')->onDelete('cascade')->nullable();
            $table->enum('condition_type', ['question_label_condition', 'go_to_step_condition', 'content_condition','signature_field','another_go_to_step_condition']); // Condition type
            $table->string('question_label')->nullable(); 
            $table->unsignedBigInteger('conditional_question_id')->nullable(); 
            $table->text('conditional_question_value')->nullable(); 
            $table->integer('conditional_check')->comment('1: is equal to; 2: is greater than; 3: is less than; 4: not equal to')->nullable();
            $table->boolean('status')->default(1); 
            $table->unsignedBigInteger('go_to_step')->nullable(); 
            $table->unsignedBigInteger('document_right_content_id')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_contract_question_conditions');
    }
};

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
        Schema::create('global_contract_multiple_choice_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade'); 
            $table->string('option_label')->nullable(); 
            $table->string('option_value')->nullable(); 
            $table->unsignedBigInteger('next_question_id')->nullable(); 
            $table->text('contract_link')->nullable();
            $table->boolean('contract_send_to_next_step')->default(false);
            $table->string('type')->nullable(); 
            $table->integer('order_id')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_contract_multiple_choice_questions');
    }
};

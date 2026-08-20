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
        Schema::create('global_contract_sub_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_condition_id')->constrained('global_contract_question_conditions')->onDelete('cascade');
            $table->integer('key')->nullable(); 
            $table->unsignedBigInteger('conditional_question_id')->nullable(); 
            $table->text('conditional_question_value')->nullable(); 
            $table->integer('conditional_check')->comment('1: is equal to; 2: is greater than; 3: is less than; 4: not equal to');
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->foreign('conditional_question_id')->references('id')->on('global_contract_questions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_contract_sub_conditions');
    }
};

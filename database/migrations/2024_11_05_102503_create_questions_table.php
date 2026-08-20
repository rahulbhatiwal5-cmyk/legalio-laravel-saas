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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('document_id');
            $table->string('standard_section_id')->nullable();
            $table->string('qid')->nullable();
            $table->string('type'); // For question type (textbox, dropdown, etc.)
            $table->boolean('is_condition')->default(false); // Condition attached (True | False)
            $table->boolean('condition_type')->nullable()
                  ->comment('1: question_label_condition; 2: go_to_step_condition; 3: if both the conditions are applied'); 
            $table->boolean('is_end')->default(false); // Indicates if this is the last question       
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};

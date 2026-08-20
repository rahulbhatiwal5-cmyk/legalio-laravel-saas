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
        Schema::create('global_contract_questions', function (Blueprint $table) {
            $table->id();
            $table->string('document_id')->nullable();
            $table->string('order_id')->nullable();
            $table->string('type'); 
            $table->boolean('is_condition')->default(false); 
            $table->boolean('condition_type')->nullable()->comment('1: question_label_condition; 2: go_to_step_condition; 3: if both the conditions are applied'); 
            $table->boolean('is_end')->default(false);  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_contract_questions');
    }
};

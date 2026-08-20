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
        Schema::create('global_contract_question_data', function (Blueprint $table) {
            $table->id();
            $table->integer('question_id')->nullable();
            $table->text('question_label')->nullable(); 
            $table->unsignedBigInteger('textbox_id')->nullable(); 
            $table->unsignedBigInteger('next_question_id')->nullable(); 
            $table->text('same_contract_link_label')->nullable();
            $table->integer('conditional_go_to_step')->nullable();
            $table->string('text_box_placeholder')->nullable(); 
            $table->longText('question_info_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_contract_question_data');
    }
};

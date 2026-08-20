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
        Schema::create('save_contract_questions', function (Blueprint $table) {
            $table->id();
            $table->string('question_type')->nullable();
            $table->integer('question_id')->nullable();
            $table->longText('answer')->nullable();
            $table->integer('saved_id')->nullable();
            $table->tinyInteger('status')->default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('save_contract_questions');
    }
};

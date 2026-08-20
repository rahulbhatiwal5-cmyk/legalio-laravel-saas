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
        Schema::create('right_section_conditions', function (Blueprint $table) {
            $table->id();
            $table->string('right_section_id')->nullable();
            $table->text('question_id')->nullable();
            $table->text('condition')->nullable();
            $table->string('question_value')->nullable();
            $table->tinyInteger('status')->default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('right_section_conditions');
    }
};

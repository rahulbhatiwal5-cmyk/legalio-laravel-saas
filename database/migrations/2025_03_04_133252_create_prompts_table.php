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
        Schema::create('prompts', function (Blueprint $table) {
            $table->id();
            $table->string('key')->nullable();
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->text('description')->nullable();
            $table->text('location')->nullable();
            $table->text('original_prompt')->nullable();
            $table->text('updated_prompt')->nullable();
            $table->text('ai_verification_model')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prompts');
    }
};

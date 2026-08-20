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
        Schema::create('prices_contents', function (Blueprint $table) {
            $table->id();
            $table->text('key')->nullable();
            $table->longText('value')->nullable();
            $table->text('file_path')->nullable();
            $table->string('type')->nullable();
            $table->text('document')->nullable();
            $table->text('button_text')->nullable();
            $table->string('price')->nullable();
            $table->longText('description')->nullable();
            $table->longText('question')->nullable();
            $table->longText('answer')->nullable();
            $table->tinyInteger('status')->default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices_contents');
    }
};

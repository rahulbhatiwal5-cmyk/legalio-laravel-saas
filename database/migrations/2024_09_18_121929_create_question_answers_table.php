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
        Schema::create('question_answers', function (Blueprint $table) {
            $table->id();
            $table->text('key')->nullable();
            $table->longtext('value')->nullable();
            $table->longtext('file_path')->nullable();
            $table->string('type')->nullable();
            $table->longtext('question')->nullable();
            $table->longtext('answer')->nullable();
            $table->string('category_id')->nullable();
            $table->longtext('meta_title')->nullable();
            $table->longtext('meta_description')->nullable();
            $table->tinyInteger('status')->default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_answers');
    }
};

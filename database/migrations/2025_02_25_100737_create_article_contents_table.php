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
        Schema::create('article_contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('article_id');
            $table->string('content_heading')->nullable();
            $table->text('content_description')->nullable();
            $table->timestamps();

            // Define foreign key constraint
            $table->foreign('article_id')->references('id')->on('knowledge_base_articles')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_contents');
    }
};

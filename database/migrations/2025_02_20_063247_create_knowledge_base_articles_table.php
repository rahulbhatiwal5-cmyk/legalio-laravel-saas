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
        Schema::create('knowledge_base_articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('title');
            $table->string('slug')->nullable();
            $table->string('seo')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('heading')->nullable();
            $table->string('sub_heading')->nullable();
            $table->text('article_overview')->nullable();
            $table->string('preview_title')->nullable();
            $table->text('preview_description')->nullable();


            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();


            $table->foreign('category_id')->references('id')->on('knowledge_base_categories')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_base_articles');
    }
};

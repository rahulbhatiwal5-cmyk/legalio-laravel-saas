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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->text('title')->nullable();
            $table->text('slug')->unique();
            $table->text('document_image')->nullable();
            $table->string('document_directory_name')->nullable();
            $table->text('document_file_path')->nullable();
            $table->longtext('short_description')->nullable();
            $table->string('btn_text')->nullable();
            $table->longtext('long_description')->nullable();
            $table->text('legal_heading')->nullable();
            $table->longtext('legal_description')->nullable();
            $table->string('legal_btn_text')->nullable();
            $table->text('legal_btn_link')->nullable();
            $table->text('legal_doc_image')->nullable();
            $table->string('directory_name')->nullable();
            $table->text('file_path')->nullable();
            $table->string('published')->nullable();
            $table->integer('doc_price')->nullable();
            $table->longtext('meta_title')->nullable();
            $table->longtext('meta_description')->nullable();
            $table->text('primary_keywords')->nullable();
            $table->longtext('secondary_keywords')->nullable();
            $table->longtext('longtail_keywords')->nullable();
            $table->longtext('high_intent_keywords')->nullable();
            $table->text('faq_title')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};

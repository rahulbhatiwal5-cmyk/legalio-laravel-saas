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
        Schema::create('faq_tag', function (Blueprint $table) {
            $table->unsignedBigInteger('faq_id');
            $table->unsignedBigInteger('tag_id');
            $table->timestamps();

            $table->primary(['faq_id', 'tag_id']);
            $table->foreign('faq_id')->references('id')->on('ai_faqs')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faq_tag');
    }
};

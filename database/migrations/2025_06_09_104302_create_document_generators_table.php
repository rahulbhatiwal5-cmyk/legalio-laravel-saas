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
        Schema::create('document_generators', function (Blueprint $table) {
            $table->id();
            $table->integer('document_id')->nullable();
            $table->text('document_name')->nullable();
            $table->longText('additional_information')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->json('ai_response')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->tinyInteger('ai_status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_generators');
    }
};

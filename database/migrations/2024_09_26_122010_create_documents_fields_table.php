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
        Schema::create('documents_fields', function (Blueprint $table) {
            $table->id();
            $table->integer('document_id')->nullable();
            $table->text('heading')->nullable();
            $table->longText('description')->nullable();
            $table->longText('description2')->nullable();
            $table->string('media_id')->nullable();
            $table->tinyInteger('status')->default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents_fields');
    }
};

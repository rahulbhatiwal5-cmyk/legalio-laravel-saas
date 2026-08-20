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
        Schema::create('global_contract_texts', function (Blueprint $table) {
            $table->id();
            $table->string('document_id')->nullable(); 
            $table->string('type')->nullable();
            $table->text('content')->nullable(); 
            $table->integer('order_id')->nullable(); 
            $table->boolean('is_condition')->default(false); 
            $table->string('text_align')->nullable();
            $table->enum('text_alignment', ['left', 'right', 'center'])->default('left');
            $table->boolean('signature_field')->default(false); 
            $table->text('content2')->nullable();
            $table->text('content3')->nullable();
            $table->boolean('secure_blur_content')->default(false); 
            $table->tinyInteger('published')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_contract_texts');
    }
};

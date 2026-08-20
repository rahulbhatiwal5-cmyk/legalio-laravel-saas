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
        Schema::create('document_right_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade'); // Link to the documents table
            $table->string('standard_section_id')->nullable();
            $table->string('type')->nullable();
            // $table->boolean('start_new_section')->default(false);
            $table->text('content')->nullable(); // Content of the document
            $table->integer('order_id')->nullable(); 
            $table->boolean('is_condition')->default(false); // true | false
            $table->string('text_align')->nullable();
            $table->enum('text_alignment', ['left', 'right', 'center'])->default('left'); // Text alignment options
            $table->boolean('signature_field')->default(false); // Signature field true | false
            // $table->string('content_class')->nullable(); // CSS class for content styling
            $table->boolean('secure_blur_content')->default(false); // true | false for secure blur
            // $table->boolean('blur_content')->default(false); // true | false for blur content
            $table->tinyInteger('published')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_right_section');
    }
};

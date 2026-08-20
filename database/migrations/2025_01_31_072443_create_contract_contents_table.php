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
        Schema::create('contract_contents', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('document_id')->nullable();
            $table->longText('html')->nullable();
            $table->tinyInteger('status')->default('0');
            $table->enum('edit_type', ['edit_text','text_only', 'full_edit'])->default('edit_text');
            $table->string('edit_order')->nullable();
            $table->integer('edit_count')->default(0);
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->foreign('parent_id')->references('id')->on('contract_contents')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_contents');
    }
};

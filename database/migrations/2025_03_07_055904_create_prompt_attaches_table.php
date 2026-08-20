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
        Schema::create('prompt_attaches', function (Blueprint $table) {
            $table->id();
            $table->string('resource_id')->nullable();
            $table->string('frontend_img_path')->nullable();
            $table->string('backend_img_path')->nullable();
            $table->unsignedBigInteger('prompt_id')->nullable();
            $table->string('page_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prompt_attaches');
    }
};

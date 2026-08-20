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
        Schema::create('login_registers', function (Blueprint $table) {
            $table->id();
            $table->string('key')->nullable();
            $table->text('title')->nullable();
            $table->text('main_heading')->nullable();
            $table->text('main_sub_heading')->nullable();
            $table->string('background_image')->nullable();
            $table->text('file_path')->nullable();
            $table->longtext('meta_title')->nullable();
            $table->longtext('meta_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_registers');
    }
};

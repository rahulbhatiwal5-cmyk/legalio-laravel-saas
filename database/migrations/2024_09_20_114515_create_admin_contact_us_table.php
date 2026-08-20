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
        Schema::create('admin_contact_us', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('background_image')->nullable();
            $table->text('background_image_path')->nullable();
            $table->string('banner_title')->nullable();
            $table->longtext('banner_description')->nullable();
            $table->string('banner_image')->nullable();
            $table->text('banner_image_path')->nullable();
            $table->text('main_title')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_contact_us');
    }
};

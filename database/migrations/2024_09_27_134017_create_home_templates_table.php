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
        Schema::create('home_templates', function (Blueprint $table) {
            $table->id();
            $table->string('media_id')->nullable();
            $table->text('heading')->nullable();
            $table->text('sub_heading')->nullable();
            $table->text('link')->nullable();
            $table->tinyInteger('status')->default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_templates');
    }
};

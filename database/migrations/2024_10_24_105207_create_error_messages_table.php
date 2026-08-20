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
        Schema::create('error_messages', function (Blueprint $table) {
            $table->id();
            $table->text('error_key')->nullable();
            $table->longtext('error_value')->nullable();
            $table->string('type')->nullable();
            $table->string('page_type')->nullable();
            $table->string('title')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('error_messages');
    }
};

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
        Schema::create('free_grant_accesses', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id')->nullable();
            $table->string('grant_type')->nullable();
            $table->tinyInteger('is_granted')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('free_grant_accesses');
    }
};

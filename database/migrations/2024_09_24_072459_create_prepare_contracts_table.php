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
        Schema::create('prepare_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('key')->nullable();
            $table->longtext('value')->nullable();
            $table->string('type')->nullable();
            $table->string('prepare_work_id')->nullable();
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
        Schema::dropIfExists('prepare_contracts');
    }
};

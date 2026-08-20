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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('type')->nullable();
            $table->json('params')->nullable();
            $table->dropColumn('file_path');

        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
          
        });
    }
};

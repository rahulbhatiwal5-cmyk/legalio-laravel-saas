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
        Schema::table('admin_contact_us', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('id');
            $table->longText('meta_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_contact_us', function (Blueprint $table) {

            $table->dropColumn('meta_title');
        });
    }
};

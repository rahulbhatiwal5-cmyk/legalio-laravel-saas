<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_faqs', function (Blueprint $table) {
            $table->boolean('is_ai')->default(0)->after('answer');
        });
    }

    public function down(): void
    {
        Schema::table('document_faqs', function (Blueprint $table) {
            $table->dropColumn('is_ai');
        });
    }
};

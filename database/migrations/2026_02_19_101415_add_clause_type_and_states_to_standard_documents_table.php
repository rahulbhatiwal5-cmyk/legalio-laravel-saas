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
        Schema::table('standard_documents', function (Blueprint $table) {
            $table->string('clause_type')->default('national')->after('status');
            $table->json('states')->nullable()->after('clause_type');
        });
    }
    
    public function down(): void
    {
        Schema::table('standard_documents', function (Blueprint $table) {
            $table->dropColumn(['clause_type', 'states']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('state_specific_clauses', function (Blueprint $table) {

            $table->string('state', 50)->nullable()->change();
            $table->string('clause_type')->default('state_specific')->after('state');

        });
    }

    public function down(): void
    {
        Schema::table('state_specific_clauses', function (Blueprint $table) {
            $table->dropColumn(['clause_type', 'states']);
            $table->string('state', 50)->nullable(false)->change();


        });
    }
};
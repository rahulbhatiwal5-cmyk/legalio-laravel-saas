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
        Schema::table('email_recovery_passwords', function (Blueprint $table) {
            $table->string('email_name');
            $table->string('email_type');
        });

        // php artisan migrate --path=/database/migrations/2025_08_06_082108_add_email_fields_to_email_recovery_passwords_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_recovery_passwords', function (Blueprint $table) {
            $table->dropColumn(['email_name', 'email_type']);
        });
    }
};

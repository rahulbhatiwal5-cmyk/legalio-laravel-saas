<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE subscription_logs
            MODIFY status ENUM(
                'trialing',
                'active',
                'paid',
                'cancel',
                'canceled',
                'incomplete',
                'incomplete_expired',
                'past_due',
                'unpaid',
                'paused'
            ) NOT NULL DEFAULT 'incomplete'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE subscription_logs
            MODIFY status ENUM(
                'active',
                'cancel',
                'incomplete'
            ) NOT NULL DEFAULT 'incomplete'
        ");
    }
};
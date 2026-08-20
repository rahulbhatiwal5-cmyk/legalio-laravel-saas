<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
   public function up(): void
    {
        DB::statement("
            ALTER TABLE subscription_logs
            MODIFY COLUMN status VARCHAR(100) NOT NULL DEFAULT 'incomplete'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE subscription_logs
            MODIFY COLUMN status ENUM(
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
};
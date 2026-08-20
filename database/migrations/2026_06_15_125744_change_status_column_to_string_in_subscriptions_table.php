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
            ALTER TABLE subscriptions
            MODIFY COLUMN status VARCHAR(100) NOT NULL DEFAULT 'incomplete'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE subscriptions
            MODIFY COLUMN status ENUM(
                'trialing',
                'active',
                'cancel',
                'incomplete',
                'past_due',
                'paid',
                'unpaid',
                'canceled',
                'incomplete_expired'
            ) NOT NULL DEFAULT 'incomplete'
        ");
    }
};

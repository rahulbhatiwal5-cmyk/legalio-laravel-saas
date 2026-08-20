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
            MODIFY status ENUM(
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

        DB::statement("
            ALTER TABLE subscriptions
            MODIFY stripe_status ENUM(
                'trialing',
                'active',
                'cancel',
                'paid',
                'incomplete',
                'past_due',
                'unpaid',
                'canceled',
                'incomplete_expired'
            ) NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE subscriptions
            MODIFY status ENUM(
                'active',
                'cancel',
                'incomplete'
            ) NOT NULL DEFAULT 'incomplete'
        ");

        DB::statement("
            ALTER TABLE subscriptions
            MODIFY stripe_status ENUM(
                'active',
                'cancel',
                'incomplete'
            ) NULL
        ");
    }
};
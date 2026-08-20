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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('file_name')->nullable();
            $table->string('directory_name')->nullable();
            $table->string('file_path')->nullable();
            $table->string('RFC')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('is_admin')->nullable();
            $table->string('role')->nullable();
            $table->boolean('is_advertising')->default(false);
            $table->timestamp('trial_ends_at')->nullable();
            $table->boolean('is_subscribed')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

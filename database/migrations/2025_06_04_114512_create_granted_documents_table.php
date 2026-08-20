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
        Schema::create('granted_documents', function (Blueprint $table) {
            $table->id();
            $table->integer('grant_access_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('order_id')->nullable();
            $table->integer('document_id')->nullable();
            $table->json('granted_document_id')->nullable();
            $table->integer('plan_id')->nullable();
            $table->string('free_interval')->nullable();
            $table->string('interval_type')->nullable();
            $table->date('start_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('granted_documents');
    }
};

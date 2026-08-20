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
        Schema::create('billing_adresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); 
            $table->string('company')->nullable(); 
            $table->string('company_2')->nullable(); 
            $table->string('address')->nullable(); 
            $table->string('city')->nullable(); 
            $table->string('state')->nullable(); 
            $table->string('postal_code')->nullable(); 
            $table->string('country')->nullable(); 
          
         
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_adresses');
    }
};

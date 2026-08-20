<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('state_specific_clauses')) { 
            Schema::create('state_specific_clauses', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description');
                $table->longText('text');
                $table->string('state', 50);
                $table->json('questions')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['state', 'is_active']);
            });
        }

        // Schema::create('contract_state_clause', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('contract_id')->constrained()->onDelete('cascade'); //  FIX
        //     $table->foreignId('state_specific_clause_id')->constrained()->onDelete('cascade');
        //     $table->integer('order')->default(0);
        //     $table->timestamps();

        //     $table->unique(['contract_id', 'state_specific_clause_id'], 'contract_state_clause_unique');
        // });
    }

    public function down()
    {
        // Schema::dropIfExists('contract_state_clause');
        Schema::dropIfExists('state_specific_clauses');
    }
};

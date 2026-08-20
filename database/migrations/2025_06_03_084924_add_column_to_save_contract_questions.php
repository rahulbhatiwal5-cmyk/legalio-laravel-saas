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
        Schema::table('save_contract_questions', function (Blueprint $table) {
            $table->longText('question_label')->nullable();
            $table->longText('attempted_value')->nullable();
            $table->text('prev_id')->nullable();
            $table->text('next_id')->nullable();
            $table->text('progress')->nullable();
            $table->text('total_steps')->nullable();
            $table->text('attempted_steps')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('save_contract_questions', function (Blueprint $table) {
            //
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parties_section_templates', function (Blueprint $table) {
            $table->id();
            $table->string('parties_type');        
            $table->string('name');               
            $table->integer('party_a_count');      
            $table->integer('party_b_count');      
            $table->text('parties_section_text')->nullable();  
            $table->text('signature_section_text')->nullable(); 
            $table->text('questions')->nullable();            
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parties_section_templates');
    }
};
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
        Schema::create('ticket_messages', function (Blueprint $table) {
          
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('sent_by', ['user', 'admin']);
            $table->foreignId('media_id')->nullable()->constrained('media')->onDelete('set null'); // Correct foreign key setup
            $table->text('message')->nullable();
            $table->boolean('seen_status')->default(false); // False = Unseen, True = Seen
            
            $table->timestamps();
     
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_messages');
    }
};

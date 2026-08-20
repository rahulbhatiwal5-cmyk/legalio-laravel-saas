<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketMessage extends Model
{
    use HasFactory;
    protected $fillable = ['ticket_id', 'user_id', 'sent_by', 'media_id', 'message', 'seen_status'];

    public function ticket() {
        return $this->belongsTo(Ticket::class);
    }

    public function sender() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function media() {
        return $this->belongsTo(Media::class, 'media_id');
    }
}

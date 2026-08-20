<?php

namespace App\Models;

use Google\Service\Batch\Message;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = ['ticket_id', 'user_id', 'reason_id', 'subject', 'status', 'seen_status'];

    protected static function boot()
    {
        parent::boot();
    
        static::creating(function ($ticket) {
            $prefix = 'TICKET';
            $date = now()->format('Ymd');
            $random = strtoupper(Str::random(6));
            $ticket->ticket_id = "{$prefix}-{$date}-{$random}";
        });
    }
    
    public function messages(){
        return $this->hasMany(TicketMessage::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}

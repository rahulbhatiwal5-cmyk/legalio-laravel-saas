<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaypalWebhookLog extends Model
{
    use HasFactory;
     protected $table = 'paypal_webhook_logs';


    protected $fillable = [
        'event_id',
        'event_type',
        'payload',
        'status',
    ];

    // protected $casts = [
    //     'payload' => 'array',
    // ];

    /**
     * Disable updated_at auto-touch if not needed
     * (Enable only if you plan to update status later)
     */
    // public $timestamps = true;

    public function scopeEventType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}

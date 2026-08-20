<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractContent extends Model
{
    protected $table = 'contract_contents';

    use HasFactory;
    protected $fillable = ['document_id', 'user_id', 'html', 'edit_type', 'type', 'session_token', 'order_id', 'parent_id'];

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id', 'id');
    }
}

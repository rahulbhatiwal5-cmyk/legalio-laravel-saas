<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRelated extends Model
{
    use HasFactory;

    Protected $fillable=[
        'document_id','related_document_id','status'
    ];
}

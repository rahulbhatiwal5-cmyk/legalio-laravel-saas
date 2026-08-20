<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecommendedSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id', 
        'standard_section_id', 
        'order_id', 
        'status'
    ];

    public function standard_section()
    {
        return $this->belongsTo(StandardDocument::class, 'standard_section_id', 'id');
    }
    

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StandardDocument extends Model
{
    use HasFactory;

    protected $fillable = ['title','slug','description','status','type','document_id'];

    protected $casts = [
        'states' => 'array',
    ];

    public function relatedQuestions()
    {
        return $this->hasMany(GlobalContractQuestion::class, 'document_id', 'id');
    }

    public function relatedTexts()
    {
        return $this->hasMany(GlobalContractText::class, 'document_id', 'id');
    }

            public function scopeForState($query, $state)
        {
            return $query->where(function ($q) use ($state) {
                $q->where('clause_type', 'national')
                ->orWhere(function ($q2) use ($state) {
                    $q2->where('clause_type', 'state_specific')
                        ->whereJsonContains('states', $state);
                });
            });
        }

        public function stateVersions(){
            return $this->hasMany(StandardDocument::class, 'parent_id');
        }
        public function parentDocument(){
            return $this->belongsTo(StandardDocument::class, 'parent_id');
        }

}

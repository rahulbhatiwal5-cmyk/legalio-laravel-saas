<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prompt extends Model
{
    use HasFactory;

    Protected $fillable =[
        'key','name','type','original_prompt','updated_prompt'
    ];

    public function PromptAttach(){
        return $this->hasMany(PromptAttach::class);
    }
}

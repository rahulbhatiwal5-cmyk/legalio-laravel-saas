<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromptAttach extends Model
{
    use HasFactory;
    protected $fillable=[
        'resource_id','prompt_id','page_type'
    ];

    public function prompt(){
        return $this->hasOne(Prompt::class,'id','prompt_id');
    }
}

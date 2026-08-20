<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HowItWork extends Model
{
    use HasFactory;

    public function works(){
        return $this->hasOne(Work::class,'id','work_id');
    }
}

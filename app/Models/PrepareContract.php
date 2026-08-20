<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrepareContract extends Model
{
    use HasFactory;

    public function media(){
        return $this->hasOne(Media::class,'id','media_id');
    }

    public function contract_work(){
        return $this->hasOne(PrepareContractWork::class,'id','prepare_work_id');
    }
}

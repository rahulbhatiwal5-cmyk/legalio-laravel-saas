<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricesContent extends Model
{
    use HasFactory;

    public function documentname(){
        return $this->hasOne(Document::class, 'id', 'document');
    }

}

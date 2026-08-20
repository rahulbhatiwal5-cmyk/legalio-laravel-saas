<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreeGrantAccess extends Model
{
    use HasFactory;

    public function grantedDocument()
    {
        return $this->hasOne(GrantedDocument::class, 'grant_access_id', 'id');
    }

    public function freeSubscription()
    {
        return $this->hasOne(FreeSubscription::class, 'grant_access_id', 'id');
    }
}

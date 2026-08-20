<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Setting;
use App\Models\UserCredit;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'file_name',
        'directory_name',
        'file_path',
        'RFC',
        'is_advertising',
        'trial_ends_at',
        'google_id',
        'password',
        'is_admin',
        'email_verified_at',
        'status',
        'is_subscribed',
        'stripe_cus_id',
        'paypal_payer_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function addresses(){
        return $this->hasMany(BillingAdress::class ,'user_id');
    }

    public function getFullNameAttribute(){
        $full_name = $this->first_name . " ".$this->last_name ;
        return $full_name;
    }

    public function getProfileImageAttribute()
    {
        $defaultImage = web_setting('user_default_image', true );

        if ($this->file_path!=null || $this->file_path!=""  ) {
            return asset($this->file_path);
        }

        return asset($defaultImage);
    }
    public function orders(){
        return $this->hasMany(Order::class );
    }

    public function freeGranted(){
        return $this->hasOne(GrantedDocument::class );
    }


    public function hasCredits()
    {
        $credit = UserCredit::where('user_id', $this->id)->first();
        
        return $credit && $credit->balance > 0;
    }

    
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Subscription;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'stripe_subscription_id'
    ];

    public function user(){
        return $this->hasOne(User::class,'id', 'user_id');
    }

    public function transaction(){
        return $this->hasOne(Transaction::class,'order_id', 'id');
    }


    public function transactions(){
        return $this->hasOne(Transaction::class,'order_id', 'id');
    }

    public function document(){
        return $this->hasOne(Document::class,'id', 'document_id');
    }

    public function getStatusAttribute($value){

        if($value==1){
            return "Succeeded";
        } else if($value==0){
            return "Incomplete";
        }else if($value==2){
            return "Refunded";
        }else if($value==3){
            return "Cancelled";
        }else{
            return "Not Found";
        }
    }

    public function markSuccess($payment_method)
    {
        $this->status = 1;
        $this->quantity = 1;
        $this->save();
            if ($this->transaction) {
            $this->transaction->status= 'succeeded';
            $this->transaction->payment_type= 'document_checkout';
            $this->transaction->amount= $this->amount ;
            $this->transaction->total_amount= $this->total_amount ;
            if($payment_method=='paypal'){
                $this->transaction->payment_intent= $this->paypal_order_id ;
            }
            $this->transaction->save();
        }
    }

    public function markFailed()
    {
        $this->status = 0;
        $this->save();
        if ($this->transaction) {
            $this->transaction->update(['status' => 'failed']);
        }
    }

    public function hasSubscription()
    {
        if ($this->order_type !== 'subscription') {
            return false;
        }

        $subscription = Subscription::where([
            ['order_id', $this->id],
            ['status', 'active']
        ])->first();

        return $subscription !== null;
    }

    public function subscription(){
        return $this->hasOne(Subscription::class,'order_id','id');
    }

}

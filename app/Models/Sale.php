<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $guarded = [];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function orders()
    {
        return $this->morphMany('App\Models\Order', 'orderable');
    }

    public function payments()
    {
        return $this->morphMany('App\Models\Payment', 'paymentable');
    }

    public function store(){
        return $this->belongsTo('App\Models\Store');
    }

    public function customer(){
        return $this->belongsTo('App\Models\Customer');
    }

    
    public function company(){
        return $this->belongsTo('App\Models\Company');
    }

    public function biller(){
        return $this->belongsTo('App\User', 'biller_id');
    }
}

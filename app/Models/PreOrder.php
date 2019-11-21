<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreOrder extends Model
{
    protected $guarded = [];

    public function items(){
        return $this->hasMany('App\Models\PreOrderItem');
    }

    public function supplier(){
        return $this->belongsTo('App\Models\Supplier');
    }

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function purchases(){
        return $this->hasMany('App\Models\Purchase', 'order_id');
    }
}

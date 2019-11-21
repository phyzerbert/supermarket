<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreOrderItem extends Model
{
    protected $guarded = [];

    public function product(){
        return $this->belongsTo('App\Models\Product');
    }

    public function purchased_items(){
        return $this->hasMany('App\Models\Order', 'pre_order_item_id');
    }
}

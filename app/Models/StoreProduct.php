<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreProduct extends Model
{
    protected $fillable = [
        'store_id', 'product_id', 'quantity',
    ];

    public function store(){
        return $this->belongsTo('App\Models\Store');
    }

    public function product(){
        return $this->belongsTo('App\Models\Product');
    }
}

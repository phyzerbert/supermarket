<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    public function category(){
        return $this->belongsTo('App\Models\Category');
    }

    public function supplier(){
        return $this->belongsTo('App\Models\Supplier');
    }

    public function barcode_symbology(){
        return $this->belongsTo('App\Models\BarcodeSymbology', 'barcode_symbology_id');
    }

    public function tax(){
        return $this->belongsTo('App\Models\Tax');
    }

    public function stores(){
        return $this->belongsToMany('App\Models\Store', 'store_products');
    }

    public function currency(){
        return $this->belongsTo(Currency::class);
    }

    public function store_products(){
        return $this->hasMany('App\Models\StoreProduct');
    }
}

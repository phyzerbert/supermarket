<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    
    protected $guarded = [];

    public function orderable(){
        return $this->morphTo();
    }

    public function sales()
    {
        return $this->morphedByMany('App\Models\Sale', 'orderable');
    }

    public function purchases()
    {
        return $this->morphedByMany('App\Models\Purchase', 'orderable');
    }

    public function product(){
        return $this->belongsTo('App\Models\Product');
    }
}

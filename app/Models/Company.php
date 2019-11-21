<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
    ];

    public function users(){
        return $this->hasMany('App\User');
    }

    public function stores(){
        return $this->hasMany('App\Models\Store');
    }

    public function purchases(){
        return $this->hasMany('App\Models\Purchase');
    }

    public function sales(){
        return $this->hasMany('App\Models\Sale');
    }
    
    public function pre_orders(){
        return $this->hasMany('App\Models\PreOrder');
    }
}

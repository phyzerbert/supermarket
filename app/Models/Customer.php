<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name', 'company', 'email', 'phone_number', 'address', 'city',
    ];

    public function sales(){
        return $this->hasMany('App\Models\Sale');
    }

    
}

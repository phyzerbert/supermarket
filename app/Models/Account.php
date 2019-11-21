<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $guarded = [];

    public function company() {
        return $this->belongsTo(Company::class);
    }
    public function currency() {
        return $this->belongsTo(Currency::class);
    }
    public function user() {
        return $this->belongsTo('App\User');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Preturn extends Model
{
    protected $guarded = [];

    public function purchase() {
        return $this->belongsTo(Purchase::class);
    }
}

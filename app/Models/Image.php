<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $guarded = [];

    public function imageable(){
        return $this->morphTo();
    }

    public function sale()
    {
        return $this->morphedByMany(Sale::class, 'imageable');
    }

    public function purchase()
    {
        return $this->morphedByMany(Purchase::class, 'imageable');
    }
}

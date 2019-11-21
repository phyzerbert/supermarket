<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
 
    protected $fillable = [
        'timestamp', 'reference_no', 'store_id', 'company_id', 'supplier_id', 'discount', 'shipping', 'returns', 'grand_total', 'credit_days', 'expiry_date', 'attachment', 'note', 'status',
    ];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function orders()
    {
        return $this->morphMany(Order::class, 'orderable');
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function store(){
        return $this->belongsTo(Store::class);
    }

    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }

    public function preturns(){
        return $this->hasMany(Preturn::class);
    }

    public function currency() {
        return $this->belongsTo(Currency::class);
    }
}

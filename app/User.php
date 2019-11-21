<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role(){
        return $this->belongsTo('App\Models\Role');
    }
    
    public function hasRole($role){
        return $this->role->slug == $role;
    }

    public function company(){
        return $this->belongsTo('App\Models\Company');
    }

    public function purchases(){
        return $this->hasMany('App\Models\Purchase');
    }

    public function sales(){
        return $this->hasMany('App\Models\Sale');
    }

    public function messages(){
        return $this->hasMany('App\Models\Message');
    }

    public function received_messages(){
        return $this->hasMany('App\Models\Message', 'receiver_id');
    }
}

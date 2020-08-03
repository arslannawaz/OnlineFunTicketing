<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasApiTokens;

    protected $fillable = [
        'name', 'email', 'password', 'api_token',
    ];


    protected $hidden = [
        'password', 'remember_token',
    ];

    public function role(){
        return $this->belongsToMany('App\Roles','user_roles','user_id','role_id');
    }

    public function roles(){
        return $this->hasOne('App\UserRole','user_id','id');
    }

}

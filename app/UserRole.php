<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $fillable = [
        'user_id', 'role_id',
    ];

    public function getRole(){
        return $this->belongsTo('App\Roles','role_id', 'id');
    }
}

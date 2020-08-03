<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ewallet extends Model
{
    protected $fillable = [
        'user_id','points'
    ];

    public function userwallet(){
        return $this->belongsTo('App\User','user_id', 'id');
    }
}

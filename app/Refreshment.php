<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Refreshment extends Model
{
    protected $fillable = [
        'user_id', 'item_name', 'status',
    ];

    public function user(){
        return $this->belongsTo('App\User','user_id', 'id');
    }

    public function refreshmentDetail(){
        return $this->hasMany('App\RefreshmentDetail','refreshment_id', 'id');
    }
}

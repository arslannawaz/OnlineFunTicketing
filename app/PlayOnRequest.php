<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlayOnRequest extends Model
{
    protected $fillable = [
        'user_id', 'movie_name',
    ];

    public function user(){
        return $this->belongsTo('App\User','user_id', 'id');
    }
}

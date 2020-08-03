<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'event_id', 'rating', 'comment', 'user_id', 'status',
    ];

    public function event(){
        return $this->belongsTo('App\Event','event_id', 'id');
    }

    public function user(){
        return $this->belongsTo('App\User','user_id', 'id');
    }
}

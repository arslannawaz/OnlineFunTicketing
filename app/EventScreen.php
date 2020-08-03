<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventScreen extends Model
{
    protected $fillable = [
        'event_id','screentype_id','price',
    ];

//    public function screentype(){
//        return $this->belongsTo('App\ScreenType');
//    }

}

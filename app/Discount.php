<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{

    protected $fillable = [
        'event_id', 'time_id', 'screen_id', 'discount', 'commercial_user_id',
    ];

    public function discountTime(){
        return $this->belongsTo('App\EventTime','time_id', 'id');
    }

    public function discountScreen(){
        return $this->belongsTo('App\ScreenType','screen_id', 'id');
    }

    public function discountEvent(){
        return $this->belongsTo('App\Event','event_id', 'id');
    }
}

<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'name','description','location','category_id','status','image', 'user_id',
    ];

    public function eventcategory(){
        return $this->belongsTo('App\EventCategory','category_id', 'id');
    }

    public function eventseats(){
        return $this->hasOne('App\Seat','event_id', 'id');
    }

    public function eventtiming(){
        return $this->hasMany('App\EventTime','event_id', 'id');
    }

    public function screentypes(){
        return $this->belongsToMany('App\ScreenType','event_screens','event_id','screentype_id')->withPivot('price');
    }

    public function eventreviews(){
        return $this->hasMany('App\Review','event_id', 'id');
    }
}

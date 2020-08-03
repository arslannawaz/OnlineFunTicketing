<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketBooking extends Model
{
    protected $fillable = [
        'event_id', 'user_id', 'time_id', 'screen_id', 'totaltickets',
    ];

    public function bookingTime(){
        return $this->belongsTo('App\EventTime','time_id', 'id');
    }

    public function bookingScreen(){
        return $this->belongsTo('App\ScreenType','screen_id', 'id');
    }

    public function bookingUser(){
        return $this->belongsTo('App\User','user_id', 'id');
    }

    public function bookingEvent(){
        return $this->belongsTo('App\Event','event_id', 'id');
    }

    public function seatNumber(){
        return $this->hasMany('App\BookingSeat','booking_id', 'id');
    }

    public function bookingPayment(){
        return $this->hasOne('App\BookingPayment','booking_id', 'id');
    }

    public function bookingRefreshment(){
        return $this->hasMany('App\BookingRefreshment','booking_id', 'id');
    }

}

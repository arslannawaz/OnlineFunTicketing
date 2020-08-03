<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingSeat extends Model
{
    protected $fillable = [
        'booking_id', 'seatnumber',
    ];
}

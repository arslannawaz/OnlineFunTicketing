<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingRefreshment extends Model
{
    protected $fillable = [
        'booking_id', 'item', 'size', 'quantity', 'price'
    ];
}

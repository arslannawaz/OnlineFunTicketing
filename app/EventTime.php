<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventTime extends Model
{
    protected $fillable = [
        'event_id','time','date'
    ];
}

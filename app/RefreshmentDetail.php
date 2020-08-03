<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RefreshmentDetail extends Model
{
    protected $fillable = [
        'refreshment_id', 'size', 'price',
    ];

}

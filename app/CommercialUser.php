<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommercialUser extends Model
{
    protected $fillable = [
        'business_name', 'category_id','user_id','contact_person','phone','description','address','city',
    ];

    public function businessType(){
        return $this->belongsTo('App\EventCategory','category_id', 'id');
    }

    public function commercialUser(){
        return $this->belongsTo('App\User','user_id', 'id');
    }

}

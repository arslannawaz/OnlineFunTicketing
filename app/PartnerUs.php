<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartnerUs extends Model
{
    protected $table = 'partner_uses';

    protected $fillable = [
        'business_name', 'category_id','email','contact_person','phone','description','address','city','file',
    ];

    public function businessType(){
        return $this->belongsTo('App\EventCategory','category_id', 'id');
    }
}

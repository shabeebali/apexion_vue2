<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    protected $table = 'phones';

    public function country(){
    	return $this->belongsTo('App\Model\Country','country_id');
    }

    public function address(){
    	return $this->belongsTo('App\Model\Address','address_id');
    }
}

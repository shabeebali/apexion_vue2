<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'addresses';

    public function phones(){
    	return $this->hasMany('App\Model\Phone','address_id');
    }

    public function customer(){
    	return $this->belongsTo('App\Model\Customer','customer_id');
    }

    public function salepersons(){
    	return $this->belongsToMany('App\User','address_saleperson','address_id','saleperson_id');
    }

    public function city(){
        return $this->belongsTo('App\Model\City','city_id');
    }

    public function state(){
        return $this->belongsTo('App\Model\State','state_id');
    }

    public function country(){
        return $this->belongsTo('App\Model\Country','country_id');
    }
}

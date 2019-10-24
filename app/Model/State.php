<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $table = 'states';

    public function cities(){
    	return $this->hasMany('App\Model\City','state_id');
    }

    public function country(){
    	return $this->belongsTo('App\Model\Country','country_id');
    }
}

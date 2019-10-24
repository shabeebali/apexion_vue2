<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'cities';

    public function state(){
    	return $this->belongsTo('App\Model\State','state_id');
    }
}

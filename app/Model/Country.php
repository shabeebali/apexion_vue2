<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';

    public function states(){
    	return $this->hasMany('App\Model\State','country_id');
    }
}

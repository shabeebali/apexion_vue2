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

    public function saleperons(){
    	return $this->belongsToMany('App\User','address_saleperson','address_id','saleperson_id')
    }
}

<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';

    public function addresses(){
    	return $this->hasMany('App\Model\Address','customer_id');
    }
}

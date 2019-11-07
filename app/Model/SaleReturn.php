<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SaleReturn extends Model
{
    protected $table = 'sales';

    public function sale()){
    	return $this->belongsTo('App\Model\Sale','sale_id');
    }

    public function items(){
    	return $this->belongsToMany('App\Model\Product','sale_return_products','return_id','product_id');
    }
}

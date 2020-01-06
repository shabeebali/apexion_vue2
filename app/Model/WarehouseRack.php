<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WarehouseRack extends Model
{
    public function warehouse()
    {
    	return $this->belongsTo('App\Model\Warehouse','warehouse_id');
    }

    public function products()
    {
    	return $this->belongsToMany('App\Model\Product','product_stocks','rack_id','product_id');
    }
}

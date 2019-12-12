<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    protected $table = 'product_stock';

    protected $guarded = ['id'];

    public function warehouse()
    {
    	return $this->belongsTo('App\Model\Warehouse','warehouse_id');
    }
}

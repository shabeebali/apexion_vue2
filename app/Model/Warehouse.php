<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $table = 'warehouses';

    public function stocks(){
        return $this->hasMany('App\Model\ProductStock','warehouse_id');
    }
}

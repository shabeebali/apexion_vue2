<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $table = 'warehouses';

    public function racks(){
        return $this->hasMany('App\Model\WarehouseRack','warehouse_id');
    }
}

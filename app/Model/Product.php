<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    public function categories(){
    	return $this->belongsToMany('App\Model\Category','product_category','product_id','category_id')->withTimestamps();
    }

    public function warehouses(){
    	return $this->belongsToMany('App\Model\Warehouse','product_warehouse','product_id','warehouse_id')->withTimestamps();
    }
    public function pricelists(){
    	return $this->belongsToMany('App\Model\Pricelist','product_pricelist','product_id','pricelist_id')->withTimestamps();
    }
    public function remarks(){
    	return $this->hasMany('App\Model\ProductRemark','product_id');
	}
	public function alias(){
    	return $this->hasMany('App\Model\ProductAlias','product_id');
	}
	public function website_info(){
    	return $this->hasOne('App\Model\ProductWebsite','product_id');
	}
}

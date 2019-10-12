<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function website_info()
    {
        return $this->hasOne('App\Model\ProductWebsite');
    }
    public function alias()
    {
        return $this->hasMany('App\Model\ProductAlias');
    }
    public function categories()
    {
        return $this->belongsToMany('App\Model\Category','product_category','product_id','category_id')->withTimestamps();
    }
    public function pricelists()
    {
    	return $this->belongsToMany('App\Model\Pricelist','product_pricelist','product_id','pricelist_id')->withPivot('margin')->withTimestamps();
    }
}

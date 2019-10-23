<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Taxonomy extends Model
{
    protected $table = 'taxonomy';

    public function categories()
    {
    	return $this->hasMany('App\Model\Category','taxonomy_id');
    }
}

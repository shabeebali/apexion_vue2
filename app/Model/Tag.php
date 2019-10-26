<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
	protected $table = 'tag';
	protected $fillable =['name'];
    public function products()
    {
        return $this->morphedByMany('App\Model\Product', 'taggable');
    }

    public function customers()
    {
        return $this->morphedByMany('App\Model\Customer', 'taggable');
    }
}

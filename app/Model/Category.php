<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Category extends Model
{
	protected $table = 'categories';
        protected $guarded = ['id'];
	public function taxonomy(){
		return $this->belongsTo('App\Model\Taxonomy');
	}

	public function dbsave($row)
	{
	   $taxonomy = Taxonomy::find($row['taxonomy_id']);
        $this->name = $row['name'];
        $this->taxonomy()->associate($taxonomy);
        if($taxonomy->in_pc == 1)
        {
        	if($taxonomy->autogen == 1){
        		$this->code = $taxonomy->next_code;
        	}
        	else{
        		$this->code = $row['code'];
        	}
        }
        else{
        	$this->code = '';
        }
        
        $this->slug = Str::slug($row['name'],'_');
        $this->save();
        return $this;
	}
}

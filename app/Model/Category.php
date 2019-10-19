<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Category extends Model
{
	protected $table = 'categories';

	public function taxonomy(){
		return $this->belongsTo('App\Model\Taxonomy');
	}

	public function dbsave(Request $request)
	{
		$taxonomy = Taxonomy::find($request->taxonomy_id);
        $this->name = $request->name;
        $this->taxonomy()->associate($taxonomy);
        if($taxonomy->in_pc == 1)
        {
        	if($taxonomy->autogen == 1){
        		$this->code = $taxonomy->next_code;
        	}
        	else{
        		$this->code = $request->code;
        	}
        }
        else{
        	$this->code = '';
        }
        
        $this->slug = Str::slug($request->name,'_');
        $this->save();
	}
}

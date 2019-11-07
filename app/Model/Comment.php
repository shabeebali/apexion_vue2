<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';
    protected $guarded = ['id'];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function user(){
    	return $this->belongsTo('App\User','created_by');
    }
}

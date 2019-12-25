<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $table = "configs";
    protected $fillable = ['name','value'];
}

<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProductMedias extends Model
{
    protected $table = 'product_medias';
    protected $fillable = ['url','product_id'];
}

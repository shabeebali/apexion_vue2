<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $table = 'taxes';

    public function rules()
    {
    	return $this->hasMany('App\Model\TaxRule','tax_id');
    }
}

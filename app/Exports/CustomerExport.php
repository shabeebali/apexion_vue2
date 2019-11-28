<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Model\Customer;
use App\Model\Address;

class CustomerExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $type = '';
    protected $method = 'create';
    public function __construct($type)
    {
        $this->type = $type;
    }

    public function collection()
    {
        if($this->type == 'name')
        {
        	return Customer::select('id','name')->get();
        }
        if($this->type == 'address'){
        	$model = Address::with('customer','phones.country','salepersons','city','state','country')->get();
        	$modified = $model->map(function($item,$key){
        		$str = '';
        		foreach ($item->phones as $phone) {
        			$str =$str. '('.$phone->country->phonecode.')'.$phone->phone.', ';
        		}
        		$sp_str = '';
        		foreach ($item->salepersons as $sp) {
        			$sp_str = $sp_str.$sp->name.', ';
        		}
        		$arr =  [
        			'id' => $item->id,
        			'customer_name' => $item->customer->name,
        			'tag_name' => $item->tag_name,
        			'address' => $item->line_1.', '.$item->line_2,
        			'pin' => $item->pin,
        			'country' => $item->country_id > 0 ? $item->country->name : '',
        			'state' => $item->state_id > 0 ? $item->state->name : '',
        			'city' => $item->city_id > 0 ? $item->city->name : '',
        			'phones'  =>$str,
        			'salepersons' => $sp_str,
        		];
        		return collect($arr);
        	});
        	return $modified;
        }
    }

    public function headings(): array
    {
    	if($this->type == 'name'){
    		return [
	            'id',
	            'name',
	        ];
    	}
    	if($this->type == 'address'){
    		return [
	            'id',
	            'customer_name',
	            'tag_name',
	            'address',
	            'pin',
	            'country',
	            'state',
	            'city',
	            'phones',
	            'salepersons'
	        ];
    	}       
    }
}

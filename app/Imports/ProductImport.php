<?php

namespace App\Imports;

use App\Model\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
class ProductImport implements ToCollection, WithHeadingRow

{
    use Importable;
    protected $method = '';
    public $taxonomies = NULL;
    public $pricelists = NULL;
    public $warehouses = NULL;
    public function __construct($method, $taxonomies, $pricelists, $warehouses)
    {
        $this->method =  $method;
        $this->taxonomies = $taxonomies;
        $this->pricelists = $pricelists;
        $this->warehouses = $warehouses;
    }
    public function collection(Collection $rows)
    {
        if($this->method == 'create')
        {
            $val_arr = [
                '*.name' => 'required|unique:products',
                '*.mrp' => 'numeric',
                '*>landing_price' => 'numeric',
                '*.gsp_customer' => 'numeric',
                '*.gsp_dealer' => 'numeric',
                '*.weight' => 'numeric',
                '*.gst' => 'required'
             ];
            foreach ($this->taxonomies as $taxonomy) {
                if($taxonomy->in_pc)
                {
                    $val_arr['*.taxonomy_'.$taxonomy->slug] = 'required';
                }
            }
            Validator::make($rows->toArray(),$val_arr,[
                'required' => 'The value is required',
                '*.name.unique' => 'The name is already taken',
                'numeric' => 'The value must be of numeric type',
            ])->validate();
            foreach ($rows as $row) {
                $product = new Product;
                $product->dbsave($row->toArray(), $this->taxonomies, $this->pricelists, $this->warehouses);
            }
        }
    }        
}

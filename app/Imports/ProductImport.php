<?php

namespace App\Imports;

use App\Model\Product;
use App\Model\Taxonomy;
use App\Model\Pricelist;
use App\Model\Warehouse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
class ProductImport implements ToCollection, WithHeadingRow

{
    use Importable;
    protected $method = '';
    public function __construct($method)
    {
        $this->method =  $method;
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
            $taxonomies = Taxonomy::all();
            $pricelists = Pricelist::all();
            $warehouses = Warehouse::all();
            foreach ($taxonomies as $taxonomy) {
                if($taxonomy->in_pc)
                {
                    $val_arr['*.'.$taxonomy->slug] = 'required';
                }
            }
            Validator::make($rows->toArray(),$val_arr)->validate();
            foreach ($rows as $row) {
                $product = new Product;
                $product->dbsave($row->toArray(), $taxonomies, $pricelists, $warehouses);
            }
        }
    }        
}

<?php

namespace App\Imports;

use App\Model\Customer;
use App\Model\Address;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerImport implements ToCollection, WithHeadingRow
{
    public $method = '';
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function __construct($method,$type)
    {
        $this->method = $method;
        $this->type = $type;
    }
    public function collection(Collection $rows)
    {
        if($this->method == 'create'){
            if($this->type == 'name'){
                $val_array = [
                    '*.name' => [
                        'required',
                    ],
                ];
                $rows = $rows->toArray();
                $validator = Validator::make($rows, $val_array, [], []);
                if($validator->fails()){
                    $e1 = new IlluminateValidationException($validator->errors());
                    $e = $validator;
                    $failures = [];
                    foreach ($e->errors()->toArray() as $attribute => $messages) {
                        $row           = strtok($attribute, '.');
                        $attributeName = strtok('');
                        $attributeName = $attributes['*.' . $attributeName] ?? $attributeName;
                        $failures[] = new Failure(
                            $row,
                            $attributeName,
                            str_replace($attribute, $attributeName, $messages),
                            $rows[$row]
                        );
                    }
                    throw new \Maatwebsite\Excel\Validators\ValidationException(
                        $e1,
                        $failures
                    );
                }
                foreach ($rows as $row) {
                    $obj = new Customer;
                    $obj->name = $row['name'];
                    $obj->save();
                }
            }
            if($this->type == 'address'){
                $val_array = [
                    '*.tag_name.value' =>'required|unique:addresses,tag_name',
                    '*.line_1' => 'required',
                ];
                $rows = $rows->toArray();
                $validator = Validator::make($rows, $val_array, [], []);
                if($validator->fails()){
                    $e1 = new IlluminateValidationException($validator->errors());
                    $e = $validator;
                    $failures = [];
                    foreach ($e->errors()->toArray() as $attribute => $messages) {
                        $row           = strtok($attribute, '.');
                        $attributeName = strtok('');
                        $attributeName = $attributes['*.' . $attributeName] ?? $attributeName;
                        $failures[] = new Failure(
                            $row,
                            $attributeName,
                            str_replace($attribute, $attributeName, $messages),
                            $rows[$row]
                        );
                    }
                    throw new \Maatwebsite\Excel\Validators\ValidationException(
                        $e1,
                        $failures
                    );
                }
                $tag_arr=[];
                foreach ($rows as $row) {
                    $customer = Customer::find($row['customer_id']);
                    $tag_arr[$row['customer_id']] = explode(" ", $customer->name);
                    $temp = explode(" ", $row['tag_name']);
                    $tag_arr[$row['customer_id']] = array_merge($row['customer_id'],$temp);
                    $tag_arr[$row['customer_id']] = array_filter($tag_arr[$row['customer_id']],function($tag){
                        if(strlen($tag) > 1){
                            return true;
                        }
                    });
                    $tag_arr[$row['customer_id']] = array_unique($tag_arr[$row['customer_id']]);
                    $tag_arr[$row['customer_id']][] = $customer->name;
                    $tag_arr[$row['customer_id']][] = $row['tag_name'];
                    foreach ($tag_arr[$row['customer_id']] as $tag) {
                        $tag_obj = Tag::firstOrCreate(['name'=>$tag]);
                        $tag_ids[] = $tag_obj->id;
                    }
                    $customer->tags()->sync($tag_ids);
                    $obj2 = new Address;
                    $obj2->tag_name = $row['tag_name'];
                    $obj2->line_1 = $row['line_1'];
                    $obj2->line_2 = $row['line_2'];
                    $obj2->pin = $row['pin'];
                    $obj2->country_id = $row['country_id'] ? $row['country_id'] : 0;
                    $obj2->state_id = $row['state_id'] ? $row['state_id'] : 0;
                    $obj2->city_id = $row['city_id'] ? $row['city_id'] : 0;
                    $obj2->init_bal = $row['init_bal'] ? $row['init_bal'] : 0;
                    $obj2->init_bal_date = $row['init_bal_date'];
                    $obj2->publish = 1;
                    $obj2->tally = 1;
                    $obj2->customer_id = $row['customer_id'];
                    $obj2->save();
                    $phones = explode(",", $row['phones'])
                    foreach ($phones as $phone) {
                        $arr = explode(";", $phone)
                        $obj3 = new Phone;
                        $obj3->country_id = $arr[0];
                        $obj3->phone = $arr[1];
                        $obj3->address_id = $obj2->id;
                        $obj3->save();
                    }
                    $sp_ids = explode(",", $row['saleperson_ids'])
                    $obj2->salepersons()->sync($sp_ids);
                }
            }
        }
    }
}

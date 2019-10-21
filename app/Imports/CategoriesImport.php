<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use App\Model\Category;
use App\Model\Taxonomy;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Validators\Failure;

class CategoriesImport implements ToCollection, WithHeadingRow 
{
    use Importable;
    /**
    * @param Collection $collection
    */
    protected $taxonomy_id = 0;
    protected $method = 'create';
    public function __construct($taxonomy_id,$method)
    {
        $this->taxonomy_id = $taxonomy_id;
        $this->method =  $method;
    }
    public function collection(Collection $rows)
    {
        if($this->method == 'create')
        {
            $taxonomy = Taxonomy::find($this->taxonomy_id);
            $val_array = [
                '*.name' => [
                    'required',
                    Rule::unique('categories')->where('taxonomy_id',$this->taxonomy_id),
                ],
                '*.code' => [
                    //Rule::requiredIf(!$taxonomy->autogen),
                    //'size:'.$taxonomy->code_length,
                    Rule::unique('categories')->where('taxonomy_id',$this->taxonomy_id),
                ]
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
                $obj = new Category;
                $row['taxonomy_id'] = $this->taxonomy_id;
                $obj->dbsave($row);
            }
        }
        if($this->method == 'update')
        {
            $taxonomy = Taxonomy::find($this->taxonomy_id);
            $rows = $rows->toArray();
            $count = 1;
            foreach ($rows as $row) {
                $val_array = [
                    'id' => 'required',
                ];
                $validator = Validator::make($row, $val_array, [], []);
                if($validator->fails()){
                    $e1 = new IlluminateValidationException($validator->errors());
                    $e = $validator;
                    $failures = [];
                    foreach ($e->errors()->toArray() as $attribute => $messages) {
                        $failures[] = new Failure(
                            $count,
                            $attribute,
                            $messages,
                            $row
                        );
                    }
                    throw new \Maatwebsite\Excel\Validators\ValidationException(
                        $e1,
                        $failures
                    );
                }
                $val_array = [
                    'name' => [
                        'required',
                        Rule::unique('categories')->where('taxonomy_id',$this->taxonomy_id)->ignore($row['id']),
                    ],
                    'code' => [
                        Rule::requiredIf(!$taxonomy->autogen),
                        'size:'.$taxonomy->code_length,
                        Rule::unique('categories')->where('taxonomy_id',$this->taxonomy_id)->ignore($row['id']),
                    ]
                ];
                $validator = Validator::make($row, $val_array, [], []);
                if($validator->fails()){
                    $e1 = new IlluminateValidationException($validator->errors());
                    $e = $validator;
                    $failures = [];
                    foreach ($e->errors()->toArray() as $attribute => $messages) {
                        $failures[] = new Failure(
                            $count,
                            $attribute,
                            $messages,
                            $row
                        );
                    }
                    throw new \Maatwebsite\Excel\Validators\ValidationException(
                        $e1,
                        $failures
                    );
                }
                $obj = Category::find($row['id']);
                $obj->name = trim($row['name']);
                $obj->slug = Str::slug($row['name'],'_');
                $obj->taxonomy_id = $this->taxonomy_id;
                if(!$taxonomy->autogen)
                {
                    $obj->code = strtoupper($row['code']);
                }
                $obj->save();
                $count++;
            }
        }
    }
}

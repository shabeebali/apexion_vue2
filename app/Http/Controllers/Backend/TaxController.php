<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Tax;
use App\Model\TaxRule;
use Illuminate\Support\Str;

class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $model = Tax::all();
        return response()->json([
            'data' => $model ? $model->toArray() : '',
            'meta' => [
                'edit' => 'true',
                'delete' => 'true',
                'create' => 'true',
            ]
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $val_arr =[
            'name' =>'required|unique:taxes',
            'value' => 'required|numeric',
        ];
        $request->validate($val_arr);
        //dd($request->toArray());
        $obj = new Tax;
        $obj->name = $request->name;
        $obj->type = $request->type;
        $obj->value = $request->value;
        $obj->apply_to_all = $request->apply_to_all == 'true' ? true : false;
        $obj->save();
        if($request->apply_to_all == 'false'){
            $rules = json_decode($request->rules,true);
            foreach ($rules as $rule) {
                $obj->rules()->create([
                    'rule_entity' => $rule['ruleEntity'],
                    'rule_attribute' => $rule['ruleAttribute'],
                    'rule_comparator' => $rule['ruleComparator'],
                    'rule_value' => $rule['value_type'] == 'select' ? implode($rule['ruleValue']) : $rule['ruleValue'],
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Tax::with('rules')->find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $val_arr =[
            'name' =>'required|unique:taxes,name,'.$id,
            'value' => 'required|numeric',
        ];
        $request->validate($val_arr);
        //dd($request->toArray());
        $obj = Tax::find($id);
        $obj->name = $request->name;
        $obj->type = $request->type;
        $obj->value = $request->value;
        $obj->apply_to_all = $request->apply_to_all == 'true' ? true : false;
        $obj->save();
        if($request->apply_to_all == 'false'){
            $rules = json_decode($request->rules,true);
            foreach ($rules as $rule) {
                $obj->rules()->firstOrcreate([
                    'rule_entity' => $rule['ruleEntity'],
                    'rule_attribute' => $rule['ruleAttribute'],
                    'rule_comparator' => $rule['ruleComparator'],
                    'rule_value' => $rule['value_type'] == 'select' ? implode(",",$rule['ruleValue']) : $rule['ruleValue'],
                ]);
            }
            $delete_ids = json_decode(($request->delete_rules_ids));
            TaxRule::destroy($delete_ids);
        }
        else{
            $obj->rules()->delete();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

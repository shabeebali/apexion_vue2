<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Customer;
use App\Model\Address;
use App\Model\Phone;
use App\Model\Tag;
use Illuminate\Support\Facades\Validator;
class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view',Customer::class);
        $user = \Auth::user();
        $data = Customer::getIndex($request);
        $model = $data['model'];
        return response()->json([
            'data' => $model ? $model->toArray() : '',
            'meta' => [
                'edit' => $user->can('update',Customer::class)? 'true': 'false',
                'delete' => $user->can('delete',Customer::class)? 'true': 'false',
                'filtered' => $data['filtered'],
                'create' => $user->can('create',Customer::class) ? 'true': 'false',
            ],
            'total' => $model->count(),
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
        $user = \Auth::user();
        $this->authorize('create',Customer::class);
        //dd(json_decode($request->addresses));
        $add_arr = json_decode($request->addresses,true);
        $request->addresses = json_decode($request->addresses);
        
        //dd($request->addresses);
        Validator::make($request->all(), [
            'name'=>'required',
        ])->validate();
        Validator::make($add_arr,[
            '*.tag_name.value' =>'required|unique:addresses,tag_name',
            '*.line_1' => 'required',
        ],[
            'unique' => 'This name has already been taken.'
        ])->validate();
        $obj = new Customer;
        $obj->name = $request->name;
        $obj->save();
        $tag_arr=[];
        $tag_arr = explode(" ",$request->name);
        foreach ($request->addresses as $address) {
            $obj2 = new Address;
            $obj2->tag_name = $address->tag_name->value;
            $obj2->line_1 = $address->line_1;
            $obj2->line_2 = $address->line_2;
            $obj2->pin = $address->pin;
            $obj2->country_id = $address->country_id ? $address->country_id : 0;
            $obj2->state_id = $address->state_id ? $address->state_id : 0;
            $obj2->city_id = $address->city_id ? $address->city_id : 0;
            $obj2->init_bal = $address->init_bal ? $address->init_bal : 0;
            $obj2->init_bal_date = $address->init_bal_date;
            $obj2->publish = $user->can('approve_customer') ? $request->approved ? 1 : 0 : 0;
            $obj2->tally = $user->can('tally_customer') ? $request->tally ? 1: 0 : 0;
            $obj2->customer_id = $obj->id;
            $obj2->save();
            foreach ($address->phones as $phone) {
                $obj3 = new Phone;
                $obj3->country_id = $phone->country_id;
                $obj3->phone = $phone->value;
                $obj3->address_id = $obj2->id;
                $obj3->save();
            }
            $temp = explode(" ", $address->tag_name->value);
            $tag_arr = array_merge($tag_arr,$temp);
        }
        $tag_arr = array_filter($tag_arr,function($tag){
            if(strlen($tag) > 1){
                return true;
            }
        });
        $tag_arr = array_unique($tag_arr);
        $tag_arr[] = $request->name;
        foreach ($request->addresses as $address) {
            $tag_arr[] = $address->tag_name->value;
        }
        foreach ($tag_arr as $tag) {
            $tag_obj = Tag::firstOrCreate(['name'=>$tag]);
            $tag_ids[] = $tag_obj->id;
        }
        $obj->tags()->syncWithoutDetaching($tag_ids);
    }

    public function check(Request $request,$id = NULL)
    {
        $add_arr = json_decode($request->addresses,true);
        if($id){

        }
        else{
            $addresses = Address::all();
            $str = '';
            foreach ($addresses as $address) {
                $address_consolidated = $address->tag_name.$address->line_1.$address->line_2.$address->pin;
                foreach ($add_arr as $req_address) {
                    $req_address_consolidated = $req_address['tag_name']['value'].$req_address['line_1'].$req_address['line_2'].$req_address['pin'];
                    similar_text($address_consolidated, $req_address_consolidated, $percent);
                    if($percent > 80){
                        $customer = Customer::find($address->id);
                        $str = '<p>'.$str.'Customer: '.$customer->name.' Address tag: '.$address->tag_name.' PIN: '.$address->pin.'</p></br>';
                    }
                }
            }
            if($str != ''){
                $str = 'Similar data in '.$str.' Do you want to continue creating?';
                return response()->json([
                    'message' => 'warning',
                    'warning' => $str,
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
        //
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
        //
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

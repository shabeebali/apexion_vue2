<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Customer;
use App\Model\Address;
use App\Model\Phone;
use App\Model\Tag;
use App\Model\Country;
use App\Model\State;
use App\Model\City;
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
        $obj->dbsave($request);
    }

    public function check(Request $request,$id = NULL)
    {
        $add_arr = json_decode($request->addresses,true);
        $addresses = Address::all();
        $str = '';
        foreach ($addresses as $address) {
            $address_consolidated = $address->tag_name.$address->line_1.$address->line_2.$address->pin;
            if($id && $address->customer_id == $id){
                continue;
            }
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer = Customer::with('addresses.phones')->find($id);
        $addresses = [];
        foreach ($customer->addresses as $address){
            $states = State::where('country_id',$address->country_id)->get();
            $states->prepend(['id'=>0,'name'=>'--']);
            $cities = City::where('state_id',$address->state_id)->get();
            $cities->prepend(['id'=>0,'name'=>'--']);
            $temp = [
                'id' => $address->id,
                'tag_name' => [
                    'value'=> $address->tag_name,
                    'error'=>'',
                ],
                'line_1' => $address->line_1,
                'line_2' => $address->line_2,
                'pin' => $address->pin,
                'country_id' => $address->country_id,
                'state_id' => $address->state_id,
                'states' => $states,
                'city_id' => $address->city_id,
                'cities' => $cities,
                'init_bal' => $address->init_bal,
                'init_bal_date' => $address->init_bal_date,
                'approved' => strval($address->publish),
                'tally' => strval($address->tally),
            ];
            $temp['phones'] = [];
            foreach ($address->phones as $phone) {
                $temp['phones'][] = [
                    'id' => $phone->id,
                    'value' => $phone->phone,
                    'country_id' => $phone->country_id,
                ];
            }
            $salepersons = $address->salepersons()->get();
            $arr = [];
            foreach ($salepersons as $saleperson) {
                $arr[] = $saleperson->id;
            }
            $temp['salepersons'] = $arr;
            $addresses[] = $temp;
        }
        return [
            'name' => [
                'value' => $customer->name,
                'error' => '',
            ],
            'addresses' => $addresses,
        ];
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
        $user = \Auth::user();
        $this->authorize('update',Customer::class);
        //dd(json_decode($request->addresses));
        $add_arr = json_decode($request->addresses,true);
        $request->addresses = json_decode($request->addresses);
        
        //dd($request->addresses);
        Validator::make($request->all(), [
            'name'=>'required',
        ])->validate();
        Validator::make($add_arr,[
            '*.tag_name.value' =>'required|unique:addresses,tag_name,'.$id.',customer_id',
            '*.line_1' => 'required',
        ],[
            'unique' => 'This name has already been taken.'
        ])->validate();
        $obj = Customer::find($id);
        $obj->dbupdate($request);
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

    public function addSearch(Request $request)
    {
        $model = Address::with('city','state','country','phones','salepersons')->where('tag_name','like','%'.$request->search.'%')->limit($request->rpp)->get();
        return $model->toArray();
    }
    public function deleteAddress($id)
    {
        $this->authorize('delete',Customer::class);
        $address = Address::find($id);
        foreach ($address->phones as $phone) {
            Phone::destroy($phone->id);
        }
        $address->salepersons()->sync([]);
        $address->delete();
    }
}

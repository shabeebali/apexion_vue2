<?php

namespace App\Model;
use App\Model\Address;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $table = 'sales';

    public function salereturns(){
    	return $this->hasMany('App\Model\SaleReturn','sale_id');
    }

    public function items(){
    	return $this->belongsToMany('App\Model\Product','sale_products','sale_id','product_id');
    }

    public function address(){
    	return $this->belongsTo('App\Model\Address','address_id');
    }

    public function saleperson(){
    	return $this->belongsTo('App\User','saleperson_id');
    }

    public static function getIndex($request)
    {
        $model = Sale::with('address','saleperson');
        $filtered = [];
        if($request->search){
            $search_terms = explode(" ",$request->search);
            foreach ($search_terms as $term) {
                $model = $model->where('order_id','like','%'.$term.'%');
            }
        }
        if($request->sortby){
            $model = $request->descending? $model->orderBy($request->sortby,'desc') : $model->orderBy($request->sortby,'asc');
        }
        if($request->page){
            $offset = ($request->page -1)*($request->rpp);
            $limit  = $request->rpp;
            $model = $model->offset($offset)->limit($limit);
        }
        if($request->addresses){
        	$add_ids = explode("-",$request->addresses);
        	$model = $model->whereIn('address_id',$add_ids);
        	foreach ($add_ids as $id) {
                $address = Address::find($id);
                $filtered[] = [
                    'text'=>'Address: '.$address->tag_name,
                    'filter'=>'addresses',
                    'type'=>'array',
                    'value'=>$id
                ];
            }
        }
        if($request->salepersons){
        	$sp_ids = explode("-",$request->salepersons);
        	$model = $model->whereIn('saleperson_id',$sp_ids);
        	foreach ($sp_ids as $id) {
                $saleperson = User::find($id);
                $filtered[] = [
                    'text'=>'Saleperson: '.$saleperson->name,
                    'filter'=>'salepersons',
                    'type'=>'array',
                    'value'=>$id
                ];
            }
        }
        $model = $model->get();
        return ['model' => $model->values(), 'filtered' => $filtered];
    }
}

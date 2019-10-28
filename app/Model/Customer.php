<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';

    public function addresses(){
    	return $this->hasMany('App\Model\Address','customer_id');
    }

    public function tags()
    {
        return $this->morphToMany('App\Model\Tag', 'taggable');
    }

    public function getSalepersons($model){
    	$addresses = $model->addresses;

    }

    public static function getIndex($request){
		$model = Customer::with('addresses.salepersons')->join('addresses','customers.id','=','addresses.customer_id');
		//dd($model->get()->toArray());
		$filtered = [];
        if($request->pending){
            $model = $model->where('publish','0');
        }
        if($request->tally){
            $model = $model->where([
                ['publish','=','1'],
                ['tally','=','0']
            ]);
        }
        if($request->search){
            $search_terms = explode(" ",$request->search);
            $tags = Tag::with('customers');
            foreach ($search_terms as $term) {
                $tags = $tags->where('name','like','%'.$term.'%');
            }
            $tags = $tags->get();
            $c_ids = [];
            foreach ($tags as $tag) {
                $c_ids = array_merge($c_ids,$tag->customers->groupBy('id')->keys()->all());
            }
            $c_ids = array_unique($c_ids);
            $model->whereIn('customers.id',$c_ids);
        }
        if($request->sortby){
            $model = $request->descending? $model->orderBy($request->sortby,'desc') : $model->orderBy($request->sortby,'asc');
        }
        if($request->page){
            $offset = ($request->page -1)*($request->rpp);
            $limit  = $request->rpp;
            $model = $model->offset($offset)->limit($limit);
        }
        $model = $model->get();
        if($request->salepersons){
        	$sp_ids = explode("-",$request->salepersons);
        	$model = $model->filter(function($item,$key) use ($sp_ids){
        		foreach ($item->addresses as $address) {
        			foreach ($address->salepersons as $saleperson) {
        				if(in_array($saleperson->id, $sp_ids)){
                            return true;
                        }
        			}
        		}
        	});
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
        return ['model' => $model->values(), 'filtered' => $filtered];
	}
}

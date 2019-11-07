<?php

namespace App\Model;

use App\User;
use App\Model\Address;
use App\Model\Phone;
use App\Model\Tag;
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

    public function comments()
    {
        return $this->morphMany('App\Model\Comment', 'commentable');
    }

    public function getSalepersons($model){
    	$addresses = $model->addresses;

    }

    
    
    public static function getIndex($request){
		$model = Customer::with([
            'addresses'=>function($q){
                $q->where('publish','1');
            },
            'addresses.salepersons'
        ]);
		//dd($model->get()->toArray());
		$filtered = [];
        if($request->pending){
            $model = Customer::with([
                'addresses'=>function($q){
                    $q->where('publish','0');
                },
                'addresses.salepersons'
            ]);
        }
        if($request->tally){
            $model = Customer::with([
                'addresses'=>function($q){
                    $q->where([
                        ['publish','=','1'],
                        ['tally','=','0']
                    ]);
                },
                'addresses.salepersons'
            ]);
        }
        if($request->search){
            $search_terms = explode(" ",$request->search);

            $tags = Tag::select();
            foreach ($search_terms as $term) {
                $tags = $tags->where('name','like','%'.$term.'%');
            }
            $tags = $tags->get();
            $c_ids = [];
            foreach ($tags as $tag) {
                $c_ids = array_merge($c_ids,$tag->customers->groupBy('id')->keys()->all());
            }
            $c_ids = array_unique($c_ids);
            $model = $model->whereIn('id',$c_ids);
        }
        if($request->sortby){
            $model = $request->descending? $model->orderBy($request->sortby,'desc') : $model->orderBy($request->sortby,'asc');
        }
        $model = $model->get();
        // FIlter out models where addresses are empty
        $model = $model->filter(function($item,$key){
            if($item->addresses->count() > 0) return true;
        });
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
        if($request->page){
            $offset = ($request->page -1)*($request->rpp);
            $limit  = $request->rpp;
            $model = $model->slice($offset,$limit);
        }
        $model->transform(function($item,$key){
            $temp = '';
            foreach ($item->addresses as $address) {
                $temp .= '<p>'.$address->tag_name.'</p>';
            }
            $item->tag_name = $temp;
            return $item;
        });

        return ['model' => $model->values(), 'filtered' => $filtered];
	}

    public function dbsave($request){
        $user = \Auth::user();
        $this->name = $request->name;
        $this->save();
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
            $obj2->publish = $user->can('approve_customer') ? $address->approved : 0;
            $obj2->tally = $user->can('tally_customer') ? $address->tally : 0;
            $obj2->customer_id = $this->id;
            $obj2->save();
            foreach ($address->phones as $phone) {
                $obj3 = new Phone;
                $obj3->country_id = $phone->country_id;
                $obj3->phone = $phone->value;
                $obj3->address_id = $obj2->id;
                $obj3->save();
            }
            $sp_ids = [];
            foreach ($address->salepersons as $saleperson) {
                $sp_ids[] = $saleperson;
            }
            $obj2->salepersons()->sync($sp_ids);
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
        $this->tags()->sync($tag_ids);
    }

    public function dbupdate($request){
        $user = \Auth::user();
        $this->name = $request->name;
        $this->save();
        $tag_arr=[];
        $tag_arr = explode(" ",$request->name);
        foreach ($request->addresses as $address) {
            if (array_key_exists('id', (array) $address)){
                $obj2 = Address::find($address->id);
            }
            else{
                $obj2 = new Address;
            }
            $obj2->tag_name = $address->tag_name->value;
            $obj2->line_1 = $address->line_1;
            $obj2->line_2 = $address->line_2;
            $obj2->pin = $address->pin;
            $obj2->country_id = $address->country_id ? $address->country_id : 0;
            $obj2->state_id = $address->state_id ? $address->state_id : 0;
            $obj2->city_id = $address->city_id ? $address->city_id : 0;
            $obj2->init_bal = $address->init_bal ? $address->init_bal : 0;
            $obj2->init_bal_date = $address->init_bal_date;
            $obj2->publish = $user->can('approve_customer') ? $address->approved : 0;
            $obj2->tally = $user->can('tally_customer') ? $address->tally : 0;
            $obj2->customer_id = $this->id;
            $obj2->save();
            foreach ($address->phones as $phone) {
                if (array_key_exists('id', (array) $phone)){
                    $obj3 = Phone::find($phone->id);
                }
                else{
                    $obj3 = new Phone;
                }
                $obj3->country_id = $phone->country_id;
                $obj3->phone = $phone->value;
                $obj3->address_id = $obj2->id;
                $obj3->save();
            }
            $sp_ids = [];
            foreach ($address->salepersons as $saleperson) {
                $sp_ids[] = $saleperson;
            }
            $obj2->salepersons()->sync($sp_ids);
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
        $this->tags()->sync($tag_ids);
    }
}

<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Category;
class Product extends Model
{
    protected $table = 'products';
    public function categories(){
    	return $this->belongsToMany('App\Model\Category','product_category','product_id','category_id')->withTimestamps();
    }

    public function warehouses(){
    	return $this->belongsToMany('App\Model\Warehouse','product_warehouse','product_id','warehouse_id')->withTimestamps();
    }
    public function pricelists(){
    	return $this->belongsToMany('App\Model\Pricelist','product_pricelist','product_id','pricelist_id')->withTimestamps();
    }
    public function remarks(){
    	return $this->hasMany('App\Model\ProductRemark','product_id');
	}
	public function alias(){
    	return $this->hasMany('App\Model\ProductAlias','product_id');
	}
	public function website_info(){
    	return $this->hasOne('App\Model\ProductWebsite','product_id');
	}
    public function status(){
        $arr = [];
        if($this->publish == 0){
            $arr[] = 'pending';
        }
        if($this->tally == 0){
            $arr[] = 'tally';
        }
        return $arr;
    }
    public function getIndex($request)
    {
        $model = Product::with('categories');
        $filtered = [];
        if($request->search){
            $model->where('name','like','%'.$request->search.'%');
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
        if($request->categories){
            $cat_ids = explode("-",$request->categories);
            $model = $model->filter(function($item,$key){
                $categories = $item->categories();
                if($categories->count() > 0){
                   foreach ($categories as $category) {
                        if(in_array($category->id, $cat_ids)){
                            return true;
                        }
                    } 
                }
                return false;
            });
            foreach ($cat_ids as $id) {
                $category = Category::find($id);
                $filtered[] = [
                    'text'=>'Category: '.$category->name,
                    'filter'=>'categories',
                    'type'=>'array',
                    'value'=>$id
                ];
            }
        }
        if($request->gst){
            $arr = explode("-",$request->gst);
            $model = $model->filter(function($item,$key){
                if(in_array($item->gst, $arr)){
                    return true;
                }
                return false;
            });
            foreach ($arr as $id) {
                $filtered[] = [
                    'text'=>'GST: '.$id.'%',
                    'filter'=>'gst',
                    'type'=>'array',
                    'value'=>$id
                ];
            }
        }
        if($request->status){
            $arr = explode("-",$request->status);
            $model = $model->filter(function($item,$key){
                $statuses = $item->status();
                if(count($statuses) > 0){
                    foreach ($statuses as $status) {
                        if(in_array($status, $arr)){
                            return true;
                        }
                    }
                }
                return false;
            });
            foreach ($arr as $id) {
                $filtered[] = [
                    'text'=>'Status: '.$id,
                    'filter'=>'status',
                    'type'=>'array',
                    'value'=>$id
                ];
            }
        }
        return ['model' => $model, 'filtered' => $filtered];
    }
}

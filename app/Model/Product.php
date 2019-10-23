<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Category;
use App\Model\ProductAlias;
use App\Model\ProductMedias;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $table = 'products';
    public function categories(){
    	return $this->belongsToMany('App\Model\Category','product_category','product_id','category_id')->withTimestamps();
    }

    public function warehouses(){
    	return $this->belongsToMany('App\Model\Warehouse','product_warehouse','product_id','warehouse_id')->withPivot('stock','batch_id','expiry_date')->withTimestamps();
    }
    public function pricelists(){
    	return $this->belongsToMany('App\Model\Pricelist','product_pricelist','product_id','pricelist_id')->withPivot('margin')->withTimestamps();
    }
    public function remarks(){
    	return $this->hasMany('App\Model\ProductRemark','product_id');
	}
	public function alias(){
    	return $this->hasMany('App\Model\ProductAlias','product_id');
	}
    public function medias(){
        return $this->hasMany('App\Model\ProductMedias','product_id');
    }
	public function website_info(){
    	return $this->hasOne('App\Model\ProductWebsite','product_id');
	}
    public function getIndex($request)
    {
        $model = Product::with('categories');
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
            $model = $model->filter(function($item,$key) use ($cat_ids){
                $categories = $item->categories()->get();
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
            $model = $model->filter(function($item,$key) use ($arr){
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
        return ['model' => $model, 'filtered' => $filtered];
    }

    public function dbsave($row, $taxonomies, $pricelists, $warehouses)
    {
        $this->name = $row['name'];
        $this->slug = Str::slug($row['name'],'_');
        $this->sku = Str::slug($row['name'],'_');
        $this->hsn = $row['hsn'];
        $this->landing_price = $row['landing_price'];
        $this->gsp_customer = $row['gsp_customer'];
        $this->gsp_dealer = $row['gsp_dealer'];
        $this->weight = $row['weight'];
        $this->gst = $row['gst'];
        $this->mrp = $row['mrp'];
        $this->save();
        $cat_ids = [];
        foreach ($taxonomies as $taxonomy) {
            $cat_ids[] = $row[$taxonomy->slug];
        }
        $this->categories()->syncWithoutDetaching($cat_ids);
        
        $pl_sync = [];
        foreach ($pricelists as $pricelist) {
            $pl_sync[$pricelist->id] = ['margin' => $row[$pricelist->slug] ];
        }
        $this->pricelists()->syncWithoutDetaching($pl_sync);
        
        $wh_sync = [];
        foreach ($warehouses as $warehouse) {
            $wh_sync[$warehouse->id] = ['stock'=>$row[$warehouse->slug],'batch_id'=>0,'expiry_date'=>today()];
        }
        $this->warehouses()->syncWithoutDetaching($wh_sync);

        $medias = explode(",", $row['medias']);
        foreach ($medias as $url) {
            $media = new ProductMedias;
            $media->url = $url;
            $media->product_id = $this->id;
            $media->save();
        }
        $aliases = explode(",", $row['aliases']);
        foreach ($aliases as $name) {
            $alias = new ProductAlias;
            $alias->alias = $name;
            $alias->product_id = $this->id;
            $alias->save();
        }
    }
}

<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Category;
use App\Model\Tag;
use App\Model\ProductAlias;
use App\Model\ProductMedias;
use Illuminate\Support\Str;
use Validator;
class Product extends Model
{
    protected $guarded = [];
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
    public function tags()
    {
        return $this->morphToMany('App\Model\Tag', 'taggable');
    }
    public static function getIndex($request)
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
            $search_terms = explode(" ",$request->search);
            $tags = Tag::with('products');
            foreach ($search_terms as $term) {
                $tags = $tags->orWhere('name','like','%'.$term.'%');
            }
            $tags = $tags->get();
            $p_ids =[];
            foreach ($tags as $tag) {
                $p_ids = array_merge($p_ids,$tag->products->groupBy('id')->keys()->all());
            }
            $p_ids = array_unique($p_ids);
            $model->whereIn('id',$p_ids);
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
                $categories = $item->categories;
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
        return ['model' => $model->values(), 'filtered' => $filtered];
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
        $this->total_stock = 0;
        $this->save();
        $cat_ids = [];
        $code = [];
        foreach ($taxonomies as $taxonomy) {
            $cat_ids[] = $row['taxonomy_'.$taxonomy->slug];
        }
        $this->categories()->syncWithoutDetaching($cat_ids);
        $sku = $this->getSku($cat_ids);
        $this->sku = $sku;
        $pl_sync = [];
        foreach ($pricelists as $pricelist) {
            $pl_sync[$pricelist->id] = ['margin' => $row['pricelist_'.$pricelist->slug] ];
        }
        $this->pricelists()->syncWithoutDetaching($pl_sync);
        
        $wh_sync = [];
        $total_stock = 0;
        foreach ($warehouses as $warehouse) {
            $wh_sync[$warehouse->id] = ['stock'=>$row['warehouse_'.$warehouse->slug],'batch_id'=>0,'expiry_date'=>today()];
            $total_stock+=$row['warehouse_'.$warehouse->slug];
        }
        $this->total_stock = $total_stock;
        $this->warehouses()->syncWithoutDetaching($wh_sync);
        if($row['medias']){
            $medias = explode(",", $row['medias']);
            foreach ($medias as $url) {
                $media = new ProductMedias;
                $media->url = $url;
                $media->product_id = $this->id;
                $media->save();
            }
        }
        if($row['aliases']){
            json_decode($row['aliases']);
            $aliases = (json_last_error() == JSON_ERROR_NONE) ? json_decode($row['aliases']): explode(",",$row['aliases']);
            foreach ($aliases as $name) {
                $alias = new ProductAlias;
                $alias->alias = $name;
                $alias->product_id = $this->id;
                $alias->save();
            }
        }
        else{
            $aliases = [];
        }
        $this->save();
        $tag_arr = [];
        $tag_arr = explode(" ",$row['name']);
        foreach ($aliases as $alias) {
            $temp = explode(" ", $alias);
            $tag_arr = array_merge($tag_arr,$temp);
        }
        $tag_arr[] = $sku;
        $tag_arr = array_filter($tag_arr,function($tag){
            if(strlen($tag) > 1){
                return true;
            }
        });
        $tag_arr = array_unique($tag_arr);
        $tag_arr[] = $row['name'];
        foreach ($aliases as $alias) {
            $tag_arr[] = $alias;
        }
        $tag_ids = [];
        foreach ($tag_arr as $tag) {
            $obj = Tag::firstOrCreate(['name'=>$tag]);
            $tag_ids[] = $obj->id;
        }
        $this->tags()->syncWithoutDetaching($tag_ids);
    }

    public function getSku($cat_ids,$id = NULL)
    {
        $pc = '';
        foreach ($cat_ids as $cat_id) {
            $category = Category::find($cat_id);
            $pc = $pc.$category->code;
        }
        $count = 0;
        $pc = $pc.str_pad($count, 3,0,STR_PAD_LEFT);
        if(!$id){

            $data['sku'] = $pc;
            while(Validator::make($data,['sku'=>'unique:products,sku'])->fails()){
                $pc = substr($pc, 0, -3);
                $count = $count + 1;
                $pc = $pc.str_pad($count, 3,0,STR_PAD_LEFT);
                $data['sku'] = $pc;
            }
        }
        else{
            $data['sku'] = $pc;
            while(Validator::make($data,['sku'=>'unique:products,sku,'.$id])->fails()){
                $pc = substr($pc, 0, -3);
                $count = $count + 1;
                $pc = $pc.str_pad($count, 3,0,STR_PAD_LEFT);
                $data['sku'] = $pc;
            }
        }
        return $pc;
    }
}

<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Category;
use App\Model\Tag;
use App\Model\ProductAlias;
use App\Model\ProductMedias;
use App\Model\ProductStock;
use Illuminate\Support\Str;
use Validator;
class Product extends Model
{
    protected $guarded = [];
    protected $table = 'products';
    public function categories(){
    	return $this->belongsToMany('App\Model\Category','product_category','product_id','category_id')->withTimestamps();
    }
    /*
    public function warehouses(){
    	return $this->belongsToMany('App\Model\Warehouse','product_warehouse','product_id','warehouse_id')->withPivot('stock','batch_id','expiry_date')->withTimestamps();
    }
    */
    public function pricelists(){
    	return $this->belongsToMany('App\Model\Pricelist','product_pricelist','product_id','pricelist_id')->withPivot('margin')->withTimestamps();
    }
    public function comments()
    {
        return $this->morphMany('App\Model\Comment', 'commentable');
    }   
	public function alias(){
    	return $this->hasMany('App\Model\ProductAlias','product_id');
	}
    public function medias(){
        return $this->hasMany('App\Model\ProductMedias','product_id');
    }
    public function stocks(){
        return $this->hasMany('App\Model\ProductStock','product_id');
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
        $model = Product::with('categories','pricelists')->where('publish','1');
        $filtered = [];
        if($request->pending){
            $model = Product::with('categories','pricelists')->where('publish','0');
        }
        if($request->tally){
            $model = Product::with('categories','pricelists')->where([
                ['publish','=','1'],
                ['tally','=','0']
            ]);
        }
        if($request->search){
            $search_terms = explode(" ",$request->search);
            $tags = Tag::with('products');
            foreach ($search_terms as $term) {
                $tags = $tags->where('name','like','%'.$term.'%');
            }
            $tags = $tags->get();
            $p_ids =[];
            foreach ($tags as $tag) {
                $p_ids = array_merge($p_ids,$tag->products->groupBy('id')->keys()->all());
            }
            $p_ids = array_unique($p_ids);
            $model = $model->whereIn('id',$p_ids);
        }
        if($request->sortby){
            $model = $request->descending? $model->orderBy($request->sortby,'desc') : $model->orderBy($request->sortby,'asc');
        }

        
        if($request->categories){
            $model = $model->join('product_category','products.id','product_category.product_id');
            $cat_ids = explode("-",$request->categories);
            foreach ($cat_ids as $id) {
                $model->where('product_category.category_id',$id);
            }
            /*
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
            */
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
            $model = $model->whereIn('gst',$arr);
            /*
            $model = $model->filter(function($item,$key) use ($arr){
                if(in_array($item->gst, $arr)){
                    return true;
                }
                return false;
            });
            */
            foreach ($arr as $id) {
                $filtered[] = [
                    'text'=>'GST: '.$id.'%',
                    'filter'=>'gst',
                    'type'=>'array',
                    'value'=>$id
                ];
            }
        }
        
        
        if($request->page){
            $offset = ($request->page -1)*($request->rpp);
            $limit  = $request->rpp;
            //$model = $model->slice($offset,$limit)->values();
            $model = $model->offset($offset)->limit($limit);
        }
        $model = $model->paginate();
        return ['model' => $model->items(), 'filtered' => $filtered,'total' => $model->total()];
    }

    public function dbsave($row, $taxonomies, $pricelists, $warehouses)
    {
        $user = \Auth::user();
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
        $this->publish = $user->can('approve_product') ? $row['approved'] : 0;
        $this->tally = $user->can('tally_product') ? $row['tally'] : 0;
        $this->save();
        $cat_ids = [];
        $code = [];
        foreach ($taxonomies as $taxonomy) {
            $cat_ids[] = $row['taxonomy_'.$taxonomy->slug];
        }
        $this->categories()->sync($cat_ids);
        $sku = $this->getSku($cat_ids);
        $this->sku = $sku;
        $pl_sync = [];
        foreach ($pricelists as $pricelist) {
            $pl_sync[$pricelist->id] = ['margin' => $row['pricelist_'.$pricelist->slug] ];
        }
        $this->pricelists()->sync($pl_sync);
        foreach ($warehouses as $warehouse) {
            $items = json_decode($row['warehouse_'.$warehouse->slug],true);
            if($items){
                foreach ($items as $item) {
                    $stock = ProductStock::firstOrCreate(
                        [
                            'product_id' => $this->id,
                            'warehouse_id' => $warehouse->id,
                            'batch' => $item['batch']
                        ],
                        [
                            'expiry_date' => $item['expiry_date'],
                            'qty' => $item['value'],
                        ]
                    );
                }
            }
        }
        $this->expirable = $row['expirable'];
        if($row['medias']){
            $medias = explode(",", $row['medias']);
            foreach ($medias as $url) {
                $media = new ProductMedias;
                $media->url = $url;
                $media->product_id = $this->id;
                $media->save();
            }
        }
        $aliases = [];
        if($row['aliases']){
            //json_decode($row['aliases']);
            //$aliases = (json_last_error() == JSON_ERROR_NONE) ? json_decode($row['aliases']): explode(",",$row['aliases']);
            foreach (json_decode($row['aliases'],true) as $alias) {
                if($alias['value'] != ''){
                    ProductAlias::firstOrCreate([
                        'alias' => $alias['value'],
                        'product_id' => $this->id
                    ]);
                    $aliases[] = $alias['value'];
                }
            }
        }
        if($row['comment']){
            $comment = new \App\Model\Comment([
                'body' => $row['comment'],
                'created_by' => \Auth::user()->id,
            ]);
            $this->comments()->save($comment);
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
        $this->tags()->sync($tag_ids);
    }

    public function dbupdate($row, $taxonomies, $pricelists, $warehouses)
    {
        $user = \Auth::user();
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
        $this->publish = $user->can('approve_product') ? $row['approved'] : 0;
        $this->tally = $user->can('tally_product') ? $row['tally'] : 0;
        $this->save();
        $cat_ids = [];
        $code = [];
        foreach ($taxonomies as $taxonomy) {
            $cat_ids[] = $row['taxonomy_'.$taxonomy->slug];
        }
        $this->categories()->sync($cat_ids);
        $sku = $this->getSku($cat_ids);
        $this->sku = $sku;
        $pl_sync = [];
        foreach ($pricelists as $pricelist) {
            $pl_sync[$pricelist->id] = ['margin' => $row['pricelist_'.$pricelist->slug] ];
        }
        $this->pricelists()->sync($pl_sync);
        foreach ($warehouses as $warehouse) {
            $items = json_decode($row['warehouse_'.$warehouse->slug],true);
            foreach ($items as $item) {
                $stock = ProductStock::updateOrCreate(
                    [
                        'product_id' => $this->id,
                        'warehouse_id' => $warehouse->id,
                        'batch' => $item['batch']
                    ],
                    [
                        'expiry_date' => $item['expiry_date'],
                        'qty' => $item['value'],
                    ]
                );
            }
        }
        $this->expirable = $row['expirable'];
        if($row['medias']){
            $medias = explode(",", $row['medias']);
            foreach ($medias as $url) {
                $media = new ProductMedias;
                $media->url = $url;
                $media->product_id = $this->id;
                $media->save();
            }
        }
        $aliases = [];
        if($row['aliases']){
            foreach (json_decode($row['aliases'],true) as $alias) {
                if(array_key_exists('id', (array)$alias)){
                    if($alias['value'] == ''){
                        ProductAlias::destroy($alias['id']);
                    }
                    else{
                        $alias_obj = ProductAlias::find($alias['id']);
                        $alias_obj->alias = $alias['value'];
                        $alias_obj->save();
                    }
                }
                else{
                    if($alias['value'] != ''){
                        ProductAlias::firstOrCreate([
                            'alias' => $alias['value'],
                            'product_id' => $this->id
                        ]);
                        $aliases[] = $alias['value'];
                    }
                }
            }
        }
        if($row['comment']){
            $comment = new \App\Model\Comment([
                'body' => $row['comment'],
                'created_by' => \Auth::user()->id,
            ]);
            $this->comments()->save($comment);
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
        $this->tags()->sync($tag_ids);
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

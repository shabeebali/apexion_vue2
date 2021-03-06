<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Model\Product;
use App\Model\Taxonomy;
use App\Model\Pricelist;
use App\Model\Warehouse;
use App\Model\Tax;
use App\Model\ProductAlias;
use App\Model\ProductMedias;
use Illuminate\Http\Request;
use App\Imports\ProductImport;
use Illuminate\Support\Facades\Storage;
use App\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProductCreated;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view',Product::class);
        $user = \Auth::user();
        $data =  Product::getIndex($request);
        $model = $data['model'];
        return response()->json([
            'data' => $model ? $model : [],
            'meta' => [
                'edit' => $user->can('update',Product::class)? 'true': 'false',
                'delete' => $user->can('delete',Product::class)? 'true': 'false',
                'filtered' => $data['filtered'],
                'create' => $user->can('create',Product::class) ? 'true': 'false',
                'import' => $user->id == 1 ? 'true' : 'false'
            ],
            'total' => $data['total'],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $taxes = Tax::all();
        $taxonomies = Taxonomy::with('categories')->get();
        return view('backend.products.add',[
            'taxes' => $taxes,
            'prev_url'=> url()->previous(),
            'taxonomies' => $taxonomies,
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
        //dd(json_decode($request->aliases));
        $val_arr = [
            'name' => 'required|unique:products',
            'mrp' => 'numeric',
            'landing_price' => 'numeric',
            'gsp_customer' => 'numeric',
            'gsp_dealer' => 'numeric',
            'weight' => 'numeric',
            'gst' => 'required'
        ];
        $taxonomies = Taxonomy::all();
        $pricelists = Pricelist::all();
        $warehouses = Warehouse::all();
        foreach ($taxonomies as $taxonomy) {
            if($taxonomy->in_pc)
            {
                $val_arr['taxonomy_'.$taxonomy->slug] = 'required';
            }
        }
        $request->validate($val_arr);

        $product = new Product;
        $product->dbsave($request->toArray(), $taxonomies, $pricelists, $warehouses);
        $users = User::with('roles')->get();
        $users = $users->filter(function($item){
            $roles = $item->roles;
            foreach ($roles as $role) {
                if($role->name == 'Admin' || $role->name == 'Super Admin'){
                    return true;
                }
            }
        });
        Notification::send($users, new ProductCreated);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::with(['categories.taxonomy','pricelists','stocks.warehouse','alias','medias','comments.user'])->find($id);
        return $product;

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $val_arr = [
            'name' => 'required|unique:products,name,'.$id,
            'mrp' => 'numeric',
            'landing_price' => 'numeric',
            'gsp_customer' => 'numeric',
            'gsp_dealer' => 'numeric',
            'weight' => 'numeric',
            'gst' => 'required'
        ];
        $taxonomies = Taxonomy::all();
        $pricelists = Pricelist::all();
        $warehouses = Warehouse::all();
        foreach ($taxonomies as $taxonomy) {
            if($taxonomy->in_pc)
            {
                $val_arr['taxonomy_'.$taxonomy->slug] = 'required';
            }
        }
        $request->validate($val_arr);

        $product = Product::find($id);
        $product->dbupdate($request->toArray(), $taxonomies, $pricelists, $warehouses);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        $product->categories()->sync([]);
        $product->pricelists()->sync([]);
        $product->stocks()->delete();
        $product->tags()->sync([]);
        $aliases = $product->alias()->get();
        if($aliases->count() > 0)
        {
            foreach ($aliases as $alias) {
                $alias->delete();
            }
        }
        $medias = $product->medias()->get();
        if($medias->count() > 0){
            foreach ($medias as $media) {
                $media->delete();
            }
        }

        $product->delete();
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'mimes:jpeg,bmp,png'
        ]);
        $path = $request->file('file')->store('products');
        return 'storage/'.$path;
    }

    public function notif_test(){
        $users = User::with('roles')->get();
        $users = $users->filter(function($item){
            $roles = $item->roles;
            foreach ($roles as $role) {
                if($role->name == 'Admin' || $role->name == 'Super Admin'){
                    return true;
                }
            }
        });
        $usr = \Auth::user();
        $usr->notify(new ProductCreated);
    }

    public function import(Request $request) 
    {
        $file = $request->file('file');
        $method =  $request->method;
        //dd($file->extension());
        if($file->extension() != 'xlsx' && $file->extension() != 'zip')
        {
            return response()->json([
                'status' => 'file_failed',
                'message' => 'Error: The uploaded file is not valid. Please try again'
            ],422);
        }
        else{
            $taxonomies = Taxonomy::all();
            $pricelists = Pricelist::all();
            $warehouses = Warehouse::all();
            $import = new ProductImport($method, $taxonomies, $pricelists, $warehouses);
            $import->import($request->file('file'));
        }
    }

    public function add_comment(Request $request, $id){
        $product = Product::find($id);
        $comment = new \App\Model\Comment([
                'body' => $request->body,
                'created_by' => \Auth::user()->id,
            ]);
        $product->comments()->save($comment);
        return response()->json([
            'body' => $request->body,
            'created_by' => \Auth::user()->id,
            'created_at' => today(),
            'user' => \Auth::user(),
        ]);
    }

    public function delete_alias(Request $request,$id)
    {
        ProductAlias::destroy($id);
    }
    public function delete_media(Request $request,$id)
    {
        ProductMedias::destroy($id);
        Storage::delete(substr($request->url,8));
    }
    public function remove_stock(Request $request, $id)
    {
        ProductStock::destroy($id);
    }

    public function getRate(Request $request)
    {
        $ids = explode(",", $request->id);
        $model = Product::with('pricelists')->whereIn('id',$ids)->get();
        if($request->pl != 'undefined' || $request->pl != 'null')
        {
            $model->transform(function($obj,$key) use ($request){
                foreach ($obj->pricelists as $pl) {
                    if($pl->id == $request->pl){
                        $obj->margin = $pl->pivot->margin;
                        break;
                    }
                }
                return $obj;
            });   
        }
        return response()->json($model);
    }
    public function getAttributes()
    {
        return [
            [
                'text'=>'HSN Code',
                'value' => 'hsn_code',
                'type' => 'text'
            ],
            [
                'text'=>'GST',
                'value' => 'gst',
                'type' => 'select',
                'items' => [
                    ['text'=>'5%', 'value' => '5'],
                    ['text'=>'12%', 'value' => '12'],
                    ['text'=>'18%', 'value' => '18'],
                ]
            ],
            [
                'text'=>'MRP',
                'value' => 'mrp',
                'type' => 'text'
            ],
            [
                'text'=>'Weight',
                'value' => 'weight',
                'type' => 'text'
            ],
        ];
    }
}

<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Model\Product;
use App\Model\Taxonomy;
use App\Model\Pricelist;
use App\Model\Warehouse;
use Illuminate\Http\Request;
use App\Imports\ProductImport;
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
        $product = new Product;
        $data =  $product->getIndex($request);
        $model = $data['model'];
        return response()->json([
            'data' => $model ? $model->toArray() : '',
            'meta' => [
                'edit' => $user->can('update',Product::class)? 'true': 'false',
                'delete' => $user->can('delete',Product::class)? 'true': 'false',
                'filtered' => $data['filtered']
            ],
            'total' => Product::count(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
                $val_arr[$taxonomy->slug] = 'required';
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
        $product = Product::with(['categories.taxonomy','pricelists','warehouses','alias','medias'])->find($id);
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
    public function update(Request $request, Product $product)
    {
        //
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
        $product->warehouses()->sync([]);
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
            $import = new ProductImport($method);
            $import->import($request->file('file'));
        }
    }
}

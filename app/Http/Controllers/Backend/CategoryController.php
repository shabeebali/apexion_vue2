<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Model\Category;
use App\Model\Taxonomy;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Exports\CategoriesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\Rule;
use App\Events\CategoryCreated;
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view',Category::class);
        $user = \Auth::user();
        $model = Category::select('id','name','code','taxonomy_id');
        $filtered = [];
        if($request->filterby){
            $taxonomy_ids = explode("-",$request->filterby);
            $model = $model->whereIn('taxonomy_id',$taxonomy_ids);
            foreach ($taxonomy_ids as $id) {
                $taxonomy = Taxonomy::find($id);
                $filtered[] = [
                    'text'=>'Taxonomy: '.$taxonomy->name,
                    'filter'=>'taxonomy',
                    'type'=>'array',
                    'value'=>$id
                ];
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
        $model = $model->get();
        return response()->json([
            'data' => $model ? $model->toArray() : '',
            'meta' => [
                'edit' => $user->can('update',Category::class)? 'true': 'false',
                'delete' => $user->can('delete',Category::class)? 'true': 'false',
                'filtered' => $filtered
            ],
            'total' => Category::count(),
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
        $this->authorize('create',Category::class);
        $request->validate([
            'name'=>'required|unique:categories',
            'code' => Rule::unique('categories')->where('taxonomy_id',$request->taxonomy_id)
        ]);
        $obj = new Category;
        $obj->dbsave($request);
        event(new CategoryCreated($obj));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        $this->authorize('view',Category::class);
        $obj = Category::find($id);
        return response()->json([
            'data'=>[
                'name'=>$obj->name,
                'taxonomy_id' => strval($obj->taxonomy_id),
                'code' => $obj->code,
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $this->authorize('update',Category::class);
        $request->validate([
            'name' => 'required|unique:categories,name,'.$id
        ]);
        $taxonomy = Taxonomy::find($request->taxonomy_id);
        $obj = Category::find($id);
        $obj->name = $request->name;
        $obj->taxonomy()->dissociate();
        $obj->taxonomy()->associate($taxonomy);
        $obj->code = $request->code;
        $obj->slug = Str::slug($request->name,'_');
        $obj->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $this->authorize('delete',Category::class);
        Category::destroy($id);
    }
    public function export() 
    {
        $this->authorize('view',Category::class);
        $filename = 'categories_'.Str::slug(today()->toDateString(),'_').'.xlsx';
        Excel::store(new CategoriesExport, $filename, 'public');
        return asset(Storage::url($filename));
    }
}

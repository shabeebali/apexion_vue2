<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Model\Category;
use App\Model\Taxonomy;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Exports\CategoriesExport;
use App\Imports\CategoriesImport;
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
        if($request->search){
            $model->where('name','like','%'.$request->search.'%');
        }
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
        $obj = $obj->dbsave($request->toArray());
        event(new CategoryCreated($obj));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->authorize('view',Category::class);
        $obj = Category::find($id);
        return response()->json([
            'data'=>[
                'name'=>$obj->name,
                'taxonomy_id' => $obj->taxonomy_id,
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
    public function update(Request $request, $id)
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
    public function destroy($id)
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

    public function import(Request $request) 
    {
        $taxonomy_id = $request->taxonomy_id;
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
            try {
                $import = new CategoriesImport($taxonomy_id,$method);
                $import->import($request->file('file'));
            } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {

                 $failures = $e->failures();
                
                 foreach ($failures as $failure) {
                    $msg = $failure->errors();
                    $messages[$failure->row()][$failure->attribute()]['message'] = $msg[0];
                 }
                 return response()->json([
                    'status' => 'failed',
                    'messages' => $messages
                ],422);
            }
           return response()->json([
                'status' => 'success',
                'message' => 'Import Completed successfully'
            ]);
        }
    }
}

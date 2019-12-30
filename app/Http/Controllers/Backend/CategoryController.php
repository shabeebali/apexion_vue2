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
        $model = Category::with('taxonomy')->select('id','name','code','taxonomy_id');
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
        $model = $model->get();
        $total = $model->count();
        if($request->page){
            $offset = ($request->page -1)*($request->rpp);
            $limit  = $request->rpp;
            $model = $model->slice($offset,$limit)->values();
        }
        return response()->json([
            'data' => $model ? $model->toArray() : '',
            'meta' => [
                'edit' => $user->can('update',Category::class)? 'true': 'false',
                'delete' => $user->can('delete',Category::class)? 'true': 'false',
                'filtered' => $filtered,
                'create' => $user->can('create',Category::class)? 'true': 'false',
                'import' => $user->id == 1 ? 'true' : 'false'
            ],
            'total' => $total,
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
        $taxonomy = Taxonomy::find($request->taxonomy_id);
        $val_array = [
            'name'=> [
                'required',
                Rule::unique('categories')->where('taxonomy_id',$request->taxonomy_id) 
            ],
        ];
        if($taxonomy->in_pc){
            $val_array['code'] = [
                'required',
                'size:'.$taxonomy->code_length,
                Rule::unique('categories')->where('taxonomy_id',$request->taxonomy_id) 
            ];
        }
        $request->validate($val_array);
        $obj = new Category;
        $obj = $obj->dbsave($request->toArray(), $taxonomy);
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
        $obj = Category::with('taxonomy')->find($id);
        return response()->json([
            'data'=> $obj->toArray()
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
        $taxonomy = Taxonomy::find($request->taxonomy_id);
        $val_array = [
            'name'=>[
                'required',
                Rule::unique('categories')->where('taxonomy_id',$request->taxonomy_id)->ignore($id)
            ]
        ];
        if($taxonomy->in_pc){
            $val_array['code'] = [
                'required',
                'size:'.$taxonomy->code_length,
                Rule::unique('categories')->where('taxonomy_id',$request->taxonomy_id)->ignore($id)
            ];
        }
        $request->validate($val_array);
        $obj = Category::find($id);
        $obj = $obj->dbupdate($request->toArray(), $taxonomy);
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

<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Model\Taxonomy;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TaxonomyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view',Taxonomy::class);
        $user = \Auth::user();
        $model = Taxonomy::all();
        return response()->json([
            'data' => $model ? $model->toArray() : '',
            'meta' => [
                'edit' => $user->can('update',Taxonomy::class)? 'true': 'false',
                'delete' => $user->can('delete',Taxonomy::class)? 'true': 'false',
            ]
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
        $this->authorize('create',Taxonomy::class);
        $request->validate([
            'name'=>'required|unique:taxonomy'
        ]);
        $obj = new Taxonomy;
        $obj->name = $request->name;
        $obj->in_pc = $request->in_pc;
        $obj->code_length = $request->code_length;
        $obj->autogen = $request->autogen;
        $obj->code_type = $request->code_type;
        $obj->slug = Str::slug($request->name,'_');

        if($request->autogen && $request->in_pc)
        {
            $next_code = '';
            $ct_arr = explode('-',$request->code_type);
            foreach ($ct_arr as $key => $value) {
                if($value == 'alpha'){
                    $next_code = $next_code.'A';
                }
                else{
                    $next_code = $next_code.'0';
                }
            }
            $obj->next_code = $next_code;
        }
        $obj->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\Taxonomy  $taxonomy
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->authorize('view',Taxonomy::class);
        $obj = Taxonomy::find($id);
        return response()->json([
            'data'=>[
                'name'=>$obj->name,
                'in_pc'=>$obj->in_pc ? '1':'0',
                'autogen' => $obj->autogen? '1': '0',
                'code_length' => strval($obj->code_length),
                'code_type' => $obj->code_type,
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\Taxonomy  $taxonomy
     * @return \Illuminate\Http\Response
     */
    public function edit(Taxonomy $taxonomy)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\Taxonomy  $taxonomy
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authorize('update',Taxonomy::class);
        $request->validate([
            'name' => 'required|unique:taxonomy,name,'.$id
        ]);
        $obj = Taxonomy::find($id);
        $obj->name = $request->name;
        $obj->in_pc = $request->in_pc;
        $obj->code_length = $request->code_length;
        $obj->autogen = $request->autogen;
        $obj->code_type = $request->code_type;
        $obj->slug = Str::slug($request->name,'_');
        $obj->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\Taxonomy  $taxonomy
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('delete',Taxonomy::class);
        Taxonomy::destroy($id);
    }
}

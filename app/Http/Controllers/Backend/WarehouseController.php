<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Model\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view',Warehouse::class);
        $user = \Auth::user();
        $model = Warehouse::all();
        return response()->json([
            'data' => $model ? $model->toArray() : '',
            'meta' => [
                'edit' => $user->can('update',Warehouse::class)? 'true': 'false',
                'delete' => $user->can('delete',Warehouse::class)? 'true': 'false',
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
        $this->authorize('create',Warehouse::class);
        $request->validate([
            'name'=>'required|unique:warehouses'
        ]);
        $wh = new Warehouse;
        $wh->name = $request->name;
        $wh->slug = Str::slug($request->name,'_');
        $wh->save();

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->authorize('view',Warehouse::class);
        $wh = Warehouse::find($id);
        return response()->json([
            'data'=>[
                'name'=>$wh->name,
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function edit(Warehouse $warehouse)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authorize('edit',Warehouse::class);
        $request->validate([
            'name' => 'required|unique:warehouses,name,'.$id
        ]);
        $wh = Warehouse::find($id);
        $wh->name = $request->name;
        $wh->slug = Str::slug($request->name,'_');
        $wh->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('delete',Warehouse::class);
        Warehouse::destroy($id);
    }
}

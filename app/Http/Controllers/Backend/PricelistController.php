<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Model\Pricelist;
use App\Model\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class PricelistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view',Pricelist::class);
        $user = \Auth::user();
        $model = Pricelist::all();
        return response()->json([
            'data' => $model ? $model->toArray() : '',
            'meta' => [
                'edit' => $user->can('update',Pricelist::class)? 'true': 'false',
                'delete' => $user->can('delete',Pricelist::class)? 'true': 'false',
                'create' => $user->can('create',Pricelist::class)? 'true': 'false',
                'so_default_pl' => Config::where('name','so_default_pl')->first()->value,
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

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create',Pricelist::class);
        $request->validate([
            'name'=>'required|unique:pricelists'
        ]);
        $pl = new Pricelist;
        $pl->name = $request->name;
        $pl->slug = Str::slug($request->name,'_');
        $pl->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\Pricelist  $pricelist
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->authorize('view',Pricelist::class);
        $pl = Pricelist::find($id);
        return response()->json([
            'data'=>[
                'name'=>$pl->name,
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\Pricelist  $pricelist
     * @return \Illuminate\Http\Response
     */
    public function edit(Pricelist $pricelist)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\Pricelist  $pricelist
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authorize('update',Pricelist::class);
        $request->validate([
            'name' => 'required|unique:pricelists,name,'.$id
        ]);
        $pl = Pricelist::find($id);
        $pl->name = $request->name;
        $pl->slug = Str::slug($request->name,'_');
        $pl->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\Pricelist  $pricelist
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('delete',Pricelist::class);
        Pricelist::destroy($id);
    }
}

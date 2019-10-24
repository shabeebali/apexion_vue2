<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\CreateUserRoleRequest;
use App\Http\Requests\EditUserRoleRequest;
class UserRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = \Auth::user();
        $this->authorize('view',Role::class);
        $roles = Role::all();
        $role = Role::find(1);
        return response()->json([
            'data'=>$roles->toArray(),
            'meta'=>[
                'edit'=> $user->can('update',Role::class)?'true':'false',
                'delete'=> 'true',
                'create'=> $user->can('create',Role::class)?'true':'false',
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
    public function store(CreateUserRoleRequest $request)
    {
        $this->authorize('create',Role::class);
        $validated = $request->validated();
        if($validated){
            $role = Role::create(['name' => $request->name]);
            if($request->permissions)
            {
                $ids = explode(",",$request->permissions);
                $role->syncPermissions($ids);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::find($id);
        $permissions = $role->permissions()->get();
        $p_ids = [];
        foreach ($permissions as $p) {
            $p_ids[] = $p->id;
        }
        return response()->json([
            'data'=>[
                'name'=>$role->name,
                'permissions'=>$p_ids,
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EditUserRoleRequest $request, $id)
    {
        $this->authorize('update',Role::class);
        $validated = $request->validated();
        if($validated){
            $role = Role::find($id);
            $role->name = $request->name;
            $role->save();
            if($request->permissions)
            {
                $ids = explode(",",$request->permissions);
                $role->syncPermissions($ids);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::find($id);
        $this->authorize('delete',$role);
        Role::destroy($id);
    }

    public function permissions()
    {
        $model = Permission::all();
        $grouped = $model->groupBy('model');
        $data = [];
        foreach ($grouped as $key => $items) {
            $row = [];
            foreach ($items as $item) {
                $name = explode("_",$item->name);
                $row[$name[0]] = $item->id;
            }
            $row['model'] = $key;
            array_push($data, $row);
        }
        $p_ids = [];
        foreach ($model as $p) {
            $p_ids[] = $p->id;
        }
        return response()->json([
            'data'=>$data,
            'permissions' => $p_ids,
        ]);
    }
}

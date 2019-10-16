<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\EditUserRequest;
use App\Http\Resources\Users as UsersResource;
use App\User;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new UsersResource(User::all());
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
    public function store(CreateUserRequest $request)
    {
        $this->authorize('create');
        $validated = $request->validated();
        if($validated){
            $user = new User;
            if($user->dbsave($request)){
                return response()->json(['message'=>'success']);
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
        $usr = User::find($id);
        $this->authorize('view', $usr);
        $roles = $usr->roles()->get();
        $role_ids = [];
        foreach ($roles as $role) {
            $role_ids[] = $role->id;
        }
        return response()->json([
            'data'=> $usr->toArray(),
            'roles'=> $role_ids
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
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EditUserRequest $request, $id)
    {
        $this->authorize('update', $this_user);
        $validated = $request->validated();
        if($validated){
            $user = User::find($id);
            $user->dbupdate($request);
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
        $this_user = User::find($id);
        $this->authorize('delete', $this_user);
        User::destroy($id);
    }

    public function change_pass(Request $request,$id)
    {
        $request->validate([
            'password' => 'required|size:8',
        ]);
        $usr = User::find($id);
        $this->authorize('change_password', $usr);
        $usr->password = bcrypt($request->password);
        $usr->save();
    }
}

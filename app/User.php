<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\EditUserRequest;
use Spatie\Permission\Models\Role;

class User extends Authenticatable
{
    use HasRoles;
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function dbsave(CreateUserRequest $request){

        $this->name = $request->name;
        $this->email = $request->email;
        $this->password = bcrypt($request->password);
        $this->save();
        if($request->roles){
            $role_ids = explode(",",$request->roles);
            foreach ($role_ids as $id) {
                $role = Role::find($id);
                $this->assignRole($role);
            }
        }
        return true;
    }
    public function dbupdate(EditUserRequest $request){
        $this->name = $request->name;
        $this->email = $request->email;
        $this->save();
        if($request->roles){
            $role_ids = explode(",",$request->roles);
            $role_names = [];
            foreach ($role_ids as $id) {
                $role = Role::find($id);
                $role_names[] = $role->name;
            }
            if(!empty($role_names))
            {
                $this->syncRoles($role_names);
            }
        }
        return true;
    }
}

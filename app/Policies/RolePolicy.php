<?php

namespace App\Policies;

use Spatie\Permission\Models\Role;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        return null;
    }
    /**
     * Determine whether the user can view any roles.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function view(User $user)
    {
        if($user->hasRole('Super Admin'))
        {
            return true;
        }
        if($user->can('view_user_role'))
        {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create roles.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if($user->hasRole('Super Admin'))
        {
            return true;
        }
        if($user->can('create_user_role'))
        {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function update(User $user)
    {
        if($user->hasRole('Super Admin'))
        {
            return true;
        }
        if($user->can('edit_user_role'))
        {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function delete(User $user, Role $role)
    {
        if($role->name == 'Super Admin'){
            return false;
        }
        if($user->hasRole('Super Admin'))
        {
            return true;
        }
        if($user->can('delete_user_role'))
        {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function restore(User $user, Role $role)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function forceDelete(User $user, Role $role)
    {
        //
    }
}

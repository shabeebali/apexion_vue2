<?php

namespace App\Policies;

use App\Model\Warehouse;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class WarehousePolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if($user->hasRole('Super Admin'))
        {
            return true;
        }
    }

    /**
     * Determine whether the user can view any warehouses.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the warehouse.
     *
     * @param  \App\User  $user
     * @param  \App\Model\Warehouse  $warehouse
     * @return mixed
     */
    public function view(User $user)
    {
        if($user->can('view_warehouse')){
            return true;
        }
    }

    /**
     * Determine whether the user can create warehouses.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if($user->can('create_warehouse')){
            return true;
        }
    }

    /**
     * Determine whether the user can update the warehouse.
     *
     * @param  \App\User  $user
     * @param  \App\Model\Warehouse  $warehouse
     * @return mixed
     */
    public function update(User $user)
    {
        if($user->can('edit_warehouse')){
            return true;
        }
    }

    /**
     * Determine whether the user can delete the warehouse.
     *
     * @param  \App\User  $user
     * @param  \App\Model\Warehouse  $warehouse
     * @return mixed
     */
    public function delete(User $user)
    {
        if($user->can('delete_warehouse')){
            return true;
        }
    }

    /**
     * Determine whether the user can restore the warehouse.
     *
     * @param  \App\User  $user
     * @param  \App\Model\Warehouse  $warehouse
     * @return mixed
     */
    public function restore(User $user, Warehouse $warehouse)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the warehouse.
     *
     * @param  \App\User  $user
     * @param  \App\Model\Warehouse  $warehouse
     * @return mixed
     */
    public function forceDelete(User $user, Warehouse $warehouse)
    {
        //
    }
}

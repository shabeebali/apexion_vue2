<?php

namespace App\Policies;

use App\Model\Pricelist;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PricelistPolicy
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
     * Determine whether the user can view any pricelists.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the pricelist.
     *
     * @param  \App\User  $user
     * @param  \App\Model\Pricelist  $pricelist
     * @return mixed
     */
    public function view(User $user)
    {
        if($user->can('view_pricelist')){
            return true;
        }
    }

    /**
     * Determine whether the user can create pricelists.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if($user->can('create_pricelist')){
            return true;
        }
    }

    /**
     * Determine whether the user can update the pricelist.
     *
     * @param  \App\User  $user
     * @param  \App\Model\Pricelist  $pricelist
     * @return mixed
     */
    public function update(User $user)
    {
        if($user->can('edit_pricelist')){
            return true;
        }
    }

    /**
     * Determine whether the user can delete the pricelist.
     *
     * @param  \App\User  $user
     * @param  \App\Model\Pricelist  $pricelist
     * @return mixed
     */
    public function delete(User $user)
    {
        if($user->can('delete_pricelist')){
            return true;
        }
    }

    /**
     * Determine whether the user can restore the pricelist.
     *
     * @param  \App\User  $user
     * @param  \App\Model\Pricelist  $pricelist
     * @return mixed
     */
    public function restore(User $user, Pricelist $pricelist)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the pricelist.
     *
     * @param  \App\User  $user
     * @param  \App\Model\Pricelist  $pricelist
     * @return mixed
     */
    public function forceDelete(User $user, Pricelist $pricelist)
    {
        //
    }
}

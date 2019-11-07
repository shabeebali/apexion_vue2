<?php

namespace App\Policies;

use App\Model\Sale;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalePolicy
{
    use HandlesAuthorization;

    public function before(User $user){
        if($user->hasRole('Super Admin'))
        {
            return true;
        }
    }

    /**
     * Determine whether the user can view any sales.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the sale.
     *
     * @param  \App\User  $user
     * @param  \App\Model\Sale  $sale
     * @return mixed
     */
    public function view(User $user, Sale $sale)
    {
        if($user->can('view_sale')){
            return true;
        }
    }

    /**
     * Determine whether the user can create sales.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if($user->can('create_sale')){
            return true;
        }    
    }

    /**
     * Determine whether the user can update the sale.
     *
     * @param  \App\User  $user
     * @param  \App\Model\Sale  $sale
     * @return mixed
     */
    public function update(User $user, Sale $sale)
    {
        if($user->can('edit_sale')){
            return true;
        }
    }

    /**
     * Determine whether the user can delete the sale.
     *
     * @param  \App\User  $user
     * @param  \App\Model\Sale  $sale
     * @return mixed
     */
    public function delete(User $user, Sale $sale)
    {
        if($user->can('delete_sale')){
            return true;
        }
    }

    /**
     * Determine whether the user can restore the sale.
     *
     * @param  \App\User  $user
     * @param  \App\Model\Sale  $sale
     * @return mixed
     */
    public function restore(User $user, Sale $sale)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the sale.
     *
     * @param  \App\User  $user
     * @param  \App\Model\Sale  $sale
     * @return mixed
     */
    public function forceDelete(User $user, Sale $sale)
    {
        //
    }
}

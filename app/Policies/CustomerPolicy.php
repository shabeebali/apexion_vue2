<?php

namespace App\Policies;

use App\Model\Customer;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
{
    use HandlesAuthorization;

    public function before(User $user){
        if($user->hasRole('Super Admin'))
        {
            return true;
        }
    }

    /**
     * Determine whether the user can view any customers.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        
    }

    /**
     * Determine whether the user can view the customer.
     *
     * @param  \App\User  $user
     * @param  \App\Model\Customer  $customer
     * @return mixed
     */
    public function view(User $user)
    {
        if($user->can('view_customer')){
            return true;
        }
    }

    /**
     * Determine whether the user can create customers.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if($user->can('create_customer')){
            return true;
        }
    }

    /**
     * Determine whether the user can update the customer.
     *
     * @param  \App\User  $user
     * @param  \AppModel\Customer  $customer
     * @return mixed
     */
    public function update(User $user)
    {
        if($user->can('edit_customer')){
            return true;
        }
    }

    /**
     * Determine whether the user can delete the customer.
     *
     * @param  \App\User  $user
     * @param  \AppModel\Customer  $customer
     * @return mixed
     */
    public function delete(User $user)
    {
        if($user->can('delete_customer')){
            return true;
        }
    }

    /**
     * Determine whether the user can restore the customer.
     *
     * @param  \App\User  $user
     * @param  \AppModel\Customer  $customer
     * @return mixed
     */
    public function restore(User $user, Customer $customer)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the customer.
     *
     * @param  \App\User  $user
     * @param  \AppModel\Customer  $customer
     * @return mixed
     */
    public function forceDelete(User $user, Customer $customer)
    {
        //
    }
}

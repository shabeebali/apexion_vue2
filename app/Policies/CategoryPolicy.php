<?php

namespace App\Policies;

use App\Model\Category;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
{
    use HandlesAuthorization;
    public function before(User $user){
        if($user->hasRole('Super Admin'))
        {
            return true;
        }
    }

    /**
     * Determine whether the user can view any categories.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the category.
     *
     * @param  \App\User  $user
     * @param  \App\Model\Category  $category
     * @return mixed
     */
    public function view(User $user)
    {
        if($user->can('view_category')){
            return true;
        }
    }

    /**
     * Determine whether the user can create categories.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if($user->can('create_category')){
            return true;
        }
    }

    /**
     * Determine whether the user can update the category.
     *
     * @param  \App\User  $user
     * @param  \App\Model\Category  $category
     * @return mixed
     */
    public function update(User $user)
    {
        if($user->can('edit_category')){
            return true;
        }
    }

    /**
     * Determine whether the user can delete the category.
     *
     * @param  \App\User  $user
     * @param  \App\Model\Category  $category
     * @return mixed
     */
    public function delete(User $user)
    {
        if($user->can('delete_category')){
            return true;
        }
    }

    /**
     * Determine whether the user can restore the category.
     *
     * @param  \App\User  $user
     * @param  \App\Model\Category  $category
     * @return mixed
     */
    public function restore(User $user, Category $category)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the category.
     *
     * @param  \App\User  $user
     * @param  \App\Model\Category  $category
     * @return mixed
     */
    public function forceDelete(User $user, Category $category)
    {
        //
    }
}

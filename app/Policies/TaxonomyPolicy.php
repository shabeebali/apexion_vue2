<?php

namespace App\Policies;

use App\Model\Taxonomy;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaxonomyPolicy
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
     * Determine whether the user can view any taxonomies.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the taxonomy.
     *
     * @param  \App\User  $user
     * @param  \App\Model\Taxonomy  $taxonomy
     * @return mixed
     */
    public function view(User $user)
    {
        if($user->can('view_taxonomy')){
            return true;
        }
    }

    /**
     * Determine whether the user can create taxonomies.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if($user->can('create_taxonomy')){
            return true;
        }
    }

    /**
     * Determine whether the user can update the taxonomy.
     *
     * @param  \App\User  $user
     * @param  \App\Model\Taxonomy  $taxonomy
     * @return mixed
     */
    public function update(User $user)
    {
        if($user->can('edit_taxonomy')){
            return true;
        }
    }

    /**
     * Determine whether the user can delete the taxonomy.
     *
     * @param  \App\User  $user
     * @param  \App\Model\Taxonomy  $taxonomy
     * @return mixed
     */
    public function delete(User $user)
    {
        if($user->can('delete_taxonomy')){
            return true;
        }
    }

    /**
     * Determine whether the user can restore the taxonomy.
     *
     * @param  \App\User  $user
     * @param  \App\Model\Taxonomy  $taxonomy
     * @return mixed
     */
    public function restore(User $user, Taxonomy $taxonomy)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the taxonomy.
     *
     * @param  \App\User  $user
     * @param  \App\Model\Taxonomy  $taxonomy
     * @return mixed
     */
    public function forceDelete(User $user, Taxonomy $taxonomy)
    {
        //
    }
}

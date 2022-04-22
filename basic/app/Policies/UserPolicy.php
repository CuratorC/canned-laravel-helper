<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy extends Policy
{
    use HandlesAuthorization;

    /**
     * index
     *
     * @param User $currentUser
     * @return mixed
     */
    public function viewAny(User $currentUser): bool
    {
        //
        return true;
    }

    /**
     * show
     *
     * @param User $currentUser
     * @param User $user
     * @return mixed
     */
    public function view(User $currentUser, User $user): bool
    {
        //
        return true;
    }

    /**
     * create, store
     *
     * @param User $currentUser
     * @return mixed
     */
    public function create(User $currentUser): bool
    {
        //
        return true;
    }

    /**
     * edit, update
     *
     * @param User $currentUser
     * @param User $user
     * @return mixed
     */
    public function update(User $currentUser, User $user): bool
    {
        //
        return true;
    }

    /**
     * destroy
     *
     * @param User $currentUser
     * @param User $user
     * @return mixed
     */
    public function delete(User $currentUser, User $user): bool
    {
        //
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $currentUser
     * @param User $user
     * @return mixed
     */
    public function restore(User $currentUser, User $user): bool
    {
        //
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $currentUser
     * @param User $user
     * @return mixed
     */
    public function forceDelete(User $currentUser, User $user): bool
    {
        //
        return true;
    }
}

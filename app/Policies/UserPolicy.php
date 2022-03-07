<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * @param  User  $user
     * @param $ability
     *
     * @return bool
     */
    public function before(User $user, $ability)
    {
        if ($user->is_admin) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the user.
     *
     * @param User $authenticatedUser
     * @param User $user
     *
     * @return mixed
     */
    public function update(User $authenticatedUser, User $user)
    {
        return $authenticatedUser->id === $user->id;
    }
}

<?php

namespace Framework\Policies;

use Framework\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class Policy
{
    use HandlesAuthorization;

    /**
     * Grant all abilities to administrator.
     *
     * @param  \Framework\Models\User  $user
     * @return bool
     */
    public function before(User $user)
    {
        if ($user->role === 'admin') {
            return true;
        }
    }
}

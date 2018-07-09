<?php

namespace Framework\Policies;

use Framework\Models\ {User, Post};

class PostPolicy extends Policy
{
    /**
     * Determine whether the user can manage the post.
     *
     * @param  \Framework\Models\User  $user
     * @param  \Framework\Models\Post  $post
     * @return mixed
     */
    public function manage(User $user, Post $post)
    {
        return $user->id === $post->user_id;
    }
}

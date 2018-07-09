<?php

namespace Framework\Policies;

use Framework\Models\ {User, Comment};

class CommentPolicy extends Policy
{
    /**
     * Determine whether the user can manage the comment.
     *
     * @param \Framework\Models\User $user
     * @param \Framework\Models\Comment $comment
     * @return mixed
     */
    public function manage(User $user, Comment $comment)
    {
        return $user->id === $comment->user_id;
    }
}

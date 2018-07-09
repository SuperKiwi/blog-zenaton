<?php

namespace Framework\Http\Controllers\Back;

use Framework\ {
    Models\Comment,
    Repositories\CommentRepository,
    Http\Controllers\Controller
};

class CommentController extends Controller
{
    use Indexable;

    /**
     * Create a new CommentController instance.
     *
     * @param  \Framework\Repositories\CommentRepository $repository
     */
    public function __construct(CommentRepository $repository)
    {
        $this->repository = $repository;

        $this->table = 'comments';
    }

    /**
     * Update "new" field for comment.
     *
     * @param  \Framework\Models\Comment $comment
     * @return \Illuminate\Http\Response
     */
    public function updateSeen(Comment $comment)
    {
        $comment->ingoing->delete ();

        return response ()->json ();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Framework\Models\Comment $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        $comment->delete ();

        return response ()->json ();
    }
}

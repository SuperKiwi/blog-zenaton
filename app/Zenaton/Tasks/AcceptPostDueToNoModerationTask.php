<?php

namespace App\Zenaton\Tasks;

use App\Models\Post;
use Zenaton\Traits\Zenatonable;
use App\Notifications\PostAccepted;
use Zenaton\Interfaces\TaskInterface;

class AcceptPostDueToNoModerationTask implements TaskInterface
{
    use Zenatonable;

    protected $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function handle()
    {
        $this->post->active = true;
        $this->post->save();

        $this->post->user->notify(new PostAccepted($this->post, false));
    }
}

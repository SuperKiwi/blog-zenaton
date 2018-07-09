<?php

namespace Framework\Zenaton\Tasks;

use Zenaton\Traits\Zenatonable;
use Framework\Notifications\PostAccepted;
use Zenaton\Interfaces\TaskInterface;

class AcceptPostDueToNoModerationTask implements TaskInterface
{
    use Zenatonable;

    protected $post;

    public function __construct($post)
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

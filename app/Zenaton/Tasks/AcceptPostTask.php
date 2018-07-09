<?php

namespace Framework\Zenaton\Tasks;

use Zenaton\Traits\Zenatonable;
use Framework\Notifications\PostAccepted;
use Zenaton\Interfaces\TaskInterface;

class AcceptPostTask implements TaskInterface
{
    use Zenatonable;

    protected $post;

    public function __construct($post)
    {
        $this->post = $post;
    }

    public function handle()
    {
        $this->post->user->notify(new PostAccepted($this->post, false));
    }
}

<?php

namespace Framework\Zenaton\Tasks;

use Zenaton\Traits\Zenatonable;
use Framework\Notifications\PostRefused;
use Zenaton\Interfaces\TaskInterface;

class RefusePostTask implements TaskInterface
{
    use Zenatonable;

    protected $post;

    public function __construct($post)
    {
        $this->post = $post;
    }

    public function handle()
    {
        $this->post->user->notify(new PostRefused($this->post));
    }
}

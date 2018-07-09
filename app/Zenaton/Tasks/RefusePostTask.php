<?php

namespace App\Zenaton\Tasks;

use Zenaton\Traits\Zenatonable;
use App\Notifications\PostRefused;
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

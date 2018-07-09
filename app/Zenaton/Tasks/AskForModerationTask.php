<?php

namespace Framework\Zenaton\Tasks;

use Notification;
use Framework\Models\User;
use Zenaton\Traits\Zenatonable;
use Zenaton\Interfaces\TaskInterface;
use Framework\Notifications\NewPostNeedsModeration;

class AskForModerationTask implements TaskInterface
{
    use Zenatonable;

    protected $post;

    public function __construct($post)
    {
        $this->post = $post;
    }

    public function handle()
    {
        $adminUsers = User::whereRole('admin')->get();

        Notification::send($adminUsers, new NewPostNeedsModeration($this->post));
    }
}

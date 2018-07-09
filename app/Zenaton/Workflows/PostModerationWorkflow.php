<?php

namespace App\Zenaton\Workflows;

use App\Models\Post;
use Zenaton\Tasks\Wait;
use Zenaton\Traits\Zenatonable;
use App\Notifications\PostRefused;
use App\Notifications\PostAccepted;
use Zenaton\Interfaces\WorkflowInterface;
use App\Zenaton\Events\PostModeratedEvent;
use App\Zenaton\Tasks\AcceptPostDueToNoModerationTask;

class PostModerationWorkflow implements WorkflowInterface
{
    use Zenatonable;

    protected $post;

    public $event;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function handle()
    {
        $this->event = (new Wait(PostModeratedEvent::class))->seconds(15)->execute();

        if ($this->event) {
            $this->event->decision ? $this->post->user->notify(new PostAccepted($this->post)) : $this->post->user->notify(new PostRefused($this->post));

            return;
        }

        (new AcceptPostDueToNoModerationTask($this->post))->execute();
    }

    public function onEvent($event)
    {
        if ($event instanceof PostModeratedEvent) {
            $this->event = $event;
        }
    }

    public function getId()
    {
        return $this->post->id;
    }
}

<?php

namespace Framework\Zenaton\Workflows;

use Framework\Models\Post;
use Zenaton\Tasks\Wait;
use Zenaton\Traits\Zenatonable;
use Framework\Zenaton\Tasks\AcceptPostTask;
use Framework\Zenaton\Tasks\RefusePostTask;
use Zenaton\Interfaces\WorkflowInterface;
use Framework\Zenaton\Events\PostModeratedEvent;
use Framework\Zenaton\Tasks\AskForModerationTask;
use Framework\Zenaton\Tasks\AcceptPostDueToNoModerationTask;

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
        (new AskForModerationTask($this->post))->execute();

        $this->event = (new Wait(PostModeratedEvent::class))->days(2)->execute();

        if ($this->event) {
            $this->event->decision ? (new AcceptPostTask($this->post))->execute() : (new RefusePostTask($this->post))->execute();

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

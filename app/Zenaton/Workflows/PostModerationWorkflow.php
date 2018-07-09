<?php

namespace App\Zenaton\Workflows;

use App\Models\Post;
use Zenaton\Tasks\Wait;
use Zenaton\Traits\Zenatonable;
use App\Zenaton\Tasks\AcceptPostTask;
use App\Zenaton\Tasks\RefusePostTask;
use Zenaton\Interfaces\WorkflowInterface;
use App\Zenaton\Events\PostModeratedEvent;
use App\Zenaton\Tasks\AskForModerationTask;
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

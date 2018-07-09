<?php

namespace Framework\Listeners;

use Framework\Models\Ingoing;
use Framework\Services\Thumb;
use Framework\Events\ModelCreated as EventModelCreated;
use Framework\Zenaton\Workflows\PostModerationWorkflow;

class ModelCreated
{
    /**
     * Handle the event.
     *
     * @param  EventModelCreated  $event
     * @return void
     */
    public function handle(EventModelCreated $event)
    {
        $event->model->ingoing()->save(new Ingoing);

        Thumb::makeThumb($event->model);

        if (class_basename($event->model) == 'Post') {
            $this->postCreated($event->model);
        }
    }

    public function postCreated($post)
    {
        if ($post->user->role == 'admin') {
            $post->moderated = true;
            $post->active = true;
            $post->save();

            return;
        }

        (new PostModerationWorkflow($post))->dispatch();
    }
}

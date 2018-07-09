<?php

namespace Framework\Listeners;

use Framework\Events\PostUpdated as EventPostUpdated;
use Framework\Services\Thumb;

class PostUpdated
{
    /**
     * Handle the event.
     *
     * @param  PostUpdated  $event
     * @return void
     */
    public function handle(EventPostUpdated $event)
    {
        if($event->model->wasChanged ('image')) {
            Thumb::makeThumb ($event->model);
        }
    }
}

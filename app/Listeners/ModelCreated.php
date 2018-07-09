<?php

namespace App\Listeners;

use Notification;
use App\Models\User;
use App\Models\Ingoing;
use App\Services\Thumb;
use App\Notifications\NewPostNeedsModeration;
use App\Events\ModelCreated as EventModelCreated;

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
            return;
        }

        $adminUsers = User::whereRole('admin')->get();

        Notification::send($adminUsers, new NewPostNeedsModeration($post));
    }
}

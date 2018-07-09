<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PostAccepted extends Notification implements ShouldQueue
{
    use Queueable;

    protected $post;

    protected $moderated;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Post $post, bool $moderated = true)
    {
        $this->post = $post;
        $this->moderated = $moderated;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $sentence = "Your post called {$this->post->title} was accepted.";
        $sentence .= !$this->moderated ? 'Please note that the post was accepted due to no moderation after 2 days.' : '';

        return (new MailMessage)
                    ->line($sentence)
                    ->action('Seed it live', url("/posts/{$this->post->slug}"));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

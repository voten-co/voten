<?php

namespace App\Notifications;

use App\Submission;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UsernameMentioned extends Notification implements ShouldBroadcast
{
    use Queueable;

    public $user;
    public $submission;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, Submission $submission)
    {
        $this->user = $user;
        $this->submission = $submission;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        if ($notifiable->settings['notify_mentions'] && $notifiable->username != $this->user->username) {
            return ['database', 'broadcast'];
        }

        return [];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        //        return (new MailMessage)
//                    ->line('The introduction to the notification.')
//                    ->action('Notification Action', url('/'))
//                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'url'    => '/c/'.$this->submission->channel_name.'/'.$this->submission->slug,
            'name'   => $this->user->username,
            'avatar' => $this->user->avatar,
            'body'   => '@'.$this->user->username.' mentioned your username at "'.$this->submission->title.'"',
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'data'       => $this->toArray($notifiable),
            'created_at' => now(),
            'read_at'    => null,
        ]);
    }
}

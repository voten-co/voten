<?php

namespace App\Notifications;

use App\User;
use App\Category;
use Illuminate\Bus\Queueable;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class BecameModerator extends Notification implements ShouldBroadcast
{
    use Queueable;
    use InteractsWithSockets, SerializesModels;

    protected $category;
    protected $role;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Category $category, $role)
    {
        $this->category = $category;
        $this->role = $role;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // return (new MailMessage)
        //             ->line('The introduction to the notification.')
        //             ->action('Notification Action', 'https://laravel.com')
        //             ->line('Thank you for using our application!');
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
            "url" => '/c/' . $this->category->name . '/mod',
            "name" => $this->category->name,
            "avatar" => $this->category->avatar,
            "body" => 'You are now moderating ' . '#' . $this->category->name,
            "category" => $this->category,
            "role" => $this->role
        ];
    }
}

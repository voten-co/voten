<?php

namespace App\Events;

use App\Message;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageCreated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $message;
    public $contact_id;
    public $user_id;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Message $message, $contact_id, $user_id)
    {
        $this->message = $message;
        $this->contact_id = $user_id;
        $this->user_id = $contact_id;
        // $this->dontBroadcastToCurrentUser();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('App.User.' . $this->user_id);
    }
}

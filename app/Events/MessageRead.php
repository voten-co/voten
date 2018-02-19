<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class MessageRead implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $message_id;
    public $contact_id;
    public $user_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message_id, $contact_id, $user_id)
    {
        $this->message_id = $message_id;
        $this->contact_id = $user_id;
        $this->user_id = $contact_id;
        // $this->dontBroadcastToCurrentUser();
    }

    /**
     *   Get the channels the event should broadcast on.
     *
     *   @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('App.User.'.$this->user_id);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'data' => [
                'user_id'    => $this->contact_id,
                'message_id' => $this->message_id,
            ],
        ];
    }
}

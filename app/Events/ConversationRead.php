<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class ConversationRead implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $contact_id;
    public $user_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($contact_id, $user_id)
    {
        $this->contact_id = $user_id;
        $this->user_id = $contact_id;
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
                'user_id' => $this->contact_id,
            ],
        ];
    }
}

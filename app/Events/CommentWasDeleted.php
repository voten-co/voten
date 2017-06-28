<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentWasDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $comment;
    public $submission;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($comment, $submission)
    {
        $this->comment = $comment;
        $this->submission = $submission;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        // return new PrivateChannel('channel-name');
    }
}

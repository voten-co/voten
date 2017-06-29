<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class CommentWasDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

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
        $this->dontBroadcastToCurrentUser();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return ['submission.'.$this->submission->slug];
    }
}

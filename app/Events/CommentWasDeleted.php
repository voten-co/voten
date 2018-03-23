<?php

namespace App\Events;

use App\Http\Resources\CommentResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class CommentWasDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public $comment;
    public $submission;
    public $deletedByAuthor;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($comment, $submission, $deletedByAuthor)
    {
        $this->comment = $comment;
        $this->submission = $submission;
        $this->deletedByAuthor = $deletedByAuthor;
        
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

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'data' => (new CommentResource($this->comment))->resolve(),
        ];
    }
}

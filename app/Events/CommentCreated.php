<?php

namespace App\Events;

use App\Comment;
use App\Http\Resources\CommentResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class CommentCreated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $comment;

    /**
     *   Create a new event instance.
     *
     *   @return void
     */
    public function __construct(Comment $comment)
    {
        $this->comment = new CommentResource($comment);

        $this->dontBroadcastToCurrentUser();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return ['submission.'.optional($this->comment->submission)->slug];
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

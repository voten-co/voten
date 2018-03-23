<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Http\Resources\CommentResource;

class CommentWasCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $comment;
    public $submission;
    public $author;
    public $parentComment;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($comment, $submission, $author, $parentComment)
    {
        $this->comment = $comment;
        $this->submission = $submission;
        $this->author = $author;
        $this->parentComment = $parentComment;

        $this->dontBroadcastToCurrentUser();
    }

   /**
    * Get the channels the event should broadcast on.
    *
    * @return Channel|array
    */
   public function broadcastOn()
   {
       return ['submission.'.optional($this->submission)->slug];
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

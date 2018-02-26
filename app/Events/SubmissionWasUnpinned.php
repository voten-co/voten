<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubmissionWasUnpinned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $submission;
    public $user_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($submission, $user_id)
    {
        $this->submission = $submission;
        $this->user_id = $user_id;
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

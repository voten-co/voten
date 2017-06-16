<?php

namespace App\Mail;

use App\Feedback;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewFeedback extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $feedback;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Feedback $feedback)
    {
        $this->user = $user;
        $this->feedback = $feedback;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $user = $this->user;
        $feedback = $this->feedback;

        return $this->markdown('emails.feedback', compact('user', 'feedback'));
    }
}

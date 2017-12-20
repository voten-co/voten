<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ChannelRemovalWarning extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $channel;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $channel)
    {
        $this->user = $user;
        $this->channel = $channel;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $user = $this->user;
        $channel = $this->channel;

        return $this->markdown('emails.channel-removal-warning', compact('user', 'channel'))
            ->subject('Channel removal warning');
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmailAddress extends Mailable
{
    use Queueable, SerializesModels;

    public $username;
    public $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($username, $token)
    {
        $this->username = $username;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $username = $this->username;
        $token = $this->token;

        return $this->markdown('emails.verify-email-address', compact('username', 'token'))
            ->subject('Verify Email Address for Voten');
    }
}

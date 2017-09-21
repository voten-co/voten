<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CategoryRemovalWarning extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $category;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $category)
    {
        $this->user = $user;
        $this->category = $category;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $user = $this->user;
        $category = $this->category;

        return $this->markdown('emails.category-removal-warning', compact('user', 'category'))
            ->subject('Channel removal warning');
    }
}

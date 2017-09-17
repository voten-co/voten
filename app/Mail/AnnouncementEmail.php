<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AnnouncementEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $heading;
    public $body;
    public $username;
    public $button_text;
    public $button_url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($title, $heading, $body, $username, $button_text, $button_url)
    {
        $this->title = $title;
        $this->heading = $heading;
        $this->body = $body;
        $this->username = $username;
        $this->button_text = $button_text;
        $this->button_url = $button_url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $title = $this->title;
        $heading = $this->heading;
        $content = $this->body;
        $username = $this->username;
        $button_text = $this->button_text;
        $button_url = $this->button_url;

        return $this->markdown('emails.announcement', compact('heading', 'content', 'username', 'button_text', 'button_url'))
            ->subject($title);
    }
}

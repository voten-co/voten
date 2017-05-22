<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    /**
     *   The attributes that are mass assignable.
     *
     *   @var array
     */
    protected $fillable = [
        'claimed_at', 'invitation', 'email', 'sent',
    ];

    /**
     * sends the invite email and marks the invitation model as sent.
     *
     * @return void
     */
    public function send()
    {
        \Mail::to($this->email)->queue(new \App\Mail\InvitedToVoten($this->email, $this->invitation));

        $this->update([
            'sent' => true,
        ]);
    }
}

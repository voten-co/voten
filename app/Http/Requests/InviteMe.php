<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class InviteMe extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request. The user must not be logged in.
     * This is should be false when the public registeration is open and we're out of invite-only programme.
     *
     * @return bool
     */
    public function authorize()
    {
        return !Auth::check();
    }

    /**
     * The email address must not be already registered for a user. Also each email
     * can only request once for invitation. This is to prevent spamming.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email|unique:users|unique:invites',
        ];
    }
}

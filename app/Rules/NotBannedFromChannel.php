<?php

namespace App\Rules;

use App\Permissions;
use Illuminate\Contracts\Validation\Rule;

class NotBannedFromChannel implements Rule
{
    use Permissions;

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->isUserBannedFromChannel(\Auth::id(), $value) === false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You have been banned from submitting to this channel.';
    }
}

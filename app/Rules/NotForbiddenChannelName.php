<?php

namespace App\Rules;

use App\ChannelForbiddenName;
use Illuminate\Contracts\Validation\Rule;

class NotForbiddenChannelName implements Rule
{
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
        return !(ChannelForbiddenName::where('name', $value)->exists());
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The name is forbidden. Please pick another one.';
    }
}

<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CurrentPassword implements Rule
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
        return confirmPassword($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Password is incorrect.';
    }
}

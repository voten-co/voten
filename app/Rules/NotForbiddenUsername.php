<?php

namespace App\Rules;

use App\UserForbiddenName;
use Illuminate\Contracts\Validation\Rule;

class NotForbiddenUsername implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

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
        return !(UserForbiddenName::where('username', $value)->exists());
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The username is forbidden. Please pick another one.';
    }
}

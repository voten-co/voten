<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use GuzzleHttp\Client;

class Recaptcha implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $client = new Client();

        $response = $client->post(
            'https://www.google.com/recaptcha/api/siteverify',

            ['form_params'=>
                [
                    'secret' => config('services.recaptcha.secret'),
                    'response' => $value
                 ]
            ]
        );

        $body = json_decode((string)$response->getBody());

        return $body->success == true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "You didn't pass the reCAPTCHA check.";
    }
}

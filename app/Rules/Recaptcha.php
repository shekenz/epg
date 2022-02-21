<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Recaptcha implements Rule
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
     * @param  string  $attribute
     * @param  mixed  $response The reCaptcha challenge response
     * @return bool
     */
    public function passes($attribute, $response)
    {

			$url = 'https://www.google.com/recaptcha/api/siteverify';
			$data = [
				'secret' => config('app.recaptcha.secret'),
				'response' => $response
			];

			// use key 'http' even if you send the request to https://...
			$options = array(
				'http' => array(
					'header'  => 'Content-type: application/x-www-form-urlencoded'."\r\n",
					'method'  => 'POST',
					'content' => http_build_query($data)
				)
			);

			$context  = stream_context_create($options);
			$jsonResponse = json_decode(file_get_contents($url, false, $context), true);

			return $jsonResponse['success'];
        
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Captcha verification failed.';
    }
}

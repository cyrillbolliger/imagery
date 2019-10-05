<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PasswordRule implements Rule
{
    private const MIN_ENTROPY = 2 ** 64;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $base = preg_match('/\d/', $value) ? 10 : 0;
        $base += preg_match('/[a-z]/', $value) ? 26 : 0;
        $base += preg_match('/[A-Z]/', $value) ? 26 : 0;
        $base += preg_match('/[^a-zA-Z0-9]/', $value) ? 10 : 0; // people tend to always use the same

        $exp     = strlen($value);
        $entropy = $base ** $exp;

        return $entropy >= self::MIN_ENTROPY;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute is too weak. Make it longer and / or more complicated.';
    }
}

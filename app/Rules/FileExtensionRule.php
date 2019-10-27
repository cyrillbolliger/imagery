<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class FileExtensionRule implements Rule
{
    private $whitelist = [];

    /**
     * Create a new rule instance.
     *
     * @param  array  $allowedExtensions
     */
    public function __construct(array $allowedExtensions)
    {
        $this->whitelist = array_map('strtolower', $allowedExtensions);
    }

    /**
     * Check if the extension of the given filename is in the whitelist
     *
     * @param $attribute
     * @param $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $extension = pathinfo($value, PATHINFO_EXTENSION);

        if ($extension) {
            return in_array($extension, $this->whitelist);
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute has no valid file extension. Allowed extensions are: '.implode(', ', $this->whitelist);
    }
}

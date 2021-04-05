<?php

namespace App\Rules;

use App\Logo\Logo;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class LogoTypeRule implements Rule
{
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
        $dir = Logo::getBaseLogoDir();

        $dark = Storage::exists($dir.DIRECTORY_SEPARATOR.$value.'-'.Logo::LOGO_COLOR_DARK.'.svg');
        $light = Storage::exists($dir.DIRECTORY_SEPARATOR.$value.'-'.Logo::LOGO_COLOR_LIGHT.'.svg');

        return $dark && $light;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Not all logo files for the given :attribute exist.';
    }
}

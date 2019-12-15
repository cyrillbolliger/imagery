<?php

namespace App\Rules;

use App\Logo;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class LogoTypeRule implements Rule
{
    /**
     * @var Logo
     */
    private $model;

    /**
     * Create a new rule instance.
     *
     * @param  Logo  $logo
     */
    public function __construct(Logo $logo)
    {
        $this->model = $logo;
    }

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
        $dir = Logo::getStorageDir();

        $white = Storage::exists($dir.DIRECTORY_SEPARATOR.$value.'-white.svg');
        $green = Storage::exists($dir.DIRECTORY_SEPARATOR.$value.'-green.svg');

        return $white && $green;
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

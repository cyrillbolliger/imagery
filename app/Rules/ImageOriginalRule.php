<?php

namespace App\Rules;

use App\Image;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Validation\Rule;

class ImageOriginalRule implements Rule
{
    /**
     * The user
     *
     * @var Authenticatable
     */
    private $user;

    /**
     * The background value
     *
     * @var string
     */
    private $background;

    /**
     * The type value
     *
     * @var string
     */
    private $type;

    /**
     * Create a new rule instance.
     *
     * @param  Authenticatable  $user
     * @param  string  $background
     * @param  string  $type
     */
    public function __construct(Authenticatable $user, string $background, string $type)
    {
        $this->user       = $user;
        $this->background = $background;
        $this->type       = $type;
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
        if ($value) {
            // only image with a custom background can have an original
            if ($this->background !== Image::BG_CUSTOM) {
                return false;
            }

            // only final images can have an original
            if ($this->type !== Image::TYPE_FINAL) {
                return false;
            }

            $original = Image::find($value)->get();
            if ($original->user_id->is($this->user)) {
                // the original image must either be uploaded by this user
                return true;
            } else {
                // or be marked as shareable
                return $original->legal && $original->legal->shared;
            }
        } else {
            // raw image never have an original
            if ($this->type === Image::TYPE_RAW) {
                return true;
            }

            // non custom images never have an original
            return $this->background !== Image::BG_CUSTOM;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "The :attribute is either not compatible with the type or the background or the original isn't yours and it isn't shareable.";
    }
}

<?php

namespace App\Rules;

use App\Image;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ImageOriginalRule implements Rule
{
    /**
     * The model that receives the validated data
     *
     * @var Model
     */
    private $model;

    /**
     * Create a new rule instance.
     *
     * @param  Model  $model
     */
    public function __construct(Model $model)
    {
        $this->model = clone $model;
        $this->model->fill(request()->toArray());
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
            if ($this->model->background !== Image::BG_CUSTOM) {
                return false;
            }

            // only final images can have an original
            if ($this->model->type !== Image::TYPE_FINAL) {
                return false;
            }

            // no image can't be derived from itself
            if ($this->model->id === $value) {
                return false;
            }

            $original = Image::find($value);

            // the original image must exist
            if ( ! $original) {
                return false;
            }

            if ($original->user->is(Auth::user())) {
                // the original image must either be uploaded by this user
                return true;
            } else {
                // or be marked as shareable
                return $original->legal && $original->legal->shared;
            }
        } else {
            // raw image never have an original
            if ($this->model->type === Image::TYPE_RAW) {
                return true;
            }

            // final images with a custom background must have an original
            if ($this->model->type === Image::TYPE_FINAL
                && $this->model->background === Image::BG_CUSTOM) {
                return false;
            }

            // non custom images never have an original
            return $this->model->background !== Image::BG_CUSTOM;
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

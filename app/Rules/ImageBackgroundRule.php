<?php

namespace App\Rules;

use App\Image;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class ImageBackgroundRule implements Rule
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
        if ($this->model->type === Image::TYPE_RAW) {
            return $value === Image::BG_CUSTOM;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'This :attribute for raw images must be set to custom. :input given.';
    }
}

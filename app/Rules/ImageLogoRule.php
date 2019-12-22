<?php

namespace App\Rules;

use App\Image;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class ImageLogoRule implements Rule
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
        if ($this->model->type === Image::TYPE_RAW && ! is_null($value)) {
            return false;
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
        return 'The :attribute can not be set for raw images.';
    }
}

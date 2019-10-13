<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class ImmutableRule implements Rule
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
        $this->model = $model;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     *
     * @return bool|string  true or false for direct validation string if we
     *                      pass it further to a built in validation rule
     */
    public function passes($attribute, $value)
    {
        // if it's a date time object equality check is more complicated
        // we therefor use the built in method for that
        if ($this->model->$attribute instanceof \Illuminate\Support\Carbon) {
            return 'date_equals:'.$this->model->$attribute->toDateTimeString();
        }

        return $this->model->$attribute === $value;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute is not mutable.';
    }
}

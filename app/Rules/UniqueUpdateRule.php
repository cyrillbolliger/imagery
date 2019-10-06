<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class UniqueUpdateRule implements Rule
{
    /**
     * The model that receives the validated data
     *
     * @var Model|null
     */
    private $model;
    /**
     * The table where the property should be unique
     *
     * @var string
     */
    private $tableName;

    /**
     * Create a new rule instance.
     *
     * @param  Model|null  $model
     * @param  string  $tableName
     */
    public function __construct(?Model $model, string $tableName)
    {
        $this->model     = $model;
        $this->tableName = $tableName;
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
        if ($this->model) {
            // it's an update
            return "unique:{$this->tableName},$attribute,{$this->model->id}";
        } else {
            // it's an insert
            return "unique:{$this->tableName}";
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute already exists.';
    }
}

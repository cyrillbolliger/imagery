<?php

namespace App\Rules;

use App\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserManagedByRule implements Rule
{
    /**
     * The user to validate (request data)
     *
     * @var User|null  null on user creation
     */
    private $user;

    /**
     * Create a new rule instance.
     *
     * @param  User|null  $user
     */
    public function __construct(?User $user)
    {
        $this->user = $user;
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
        if ( ! $this->isChanged($value)) {
            return true;
        }

        return Auth::user()->canManageGroup($value);
    }

    /**
     * Indicate if the given value has changed
     *
     * @param  bool  $value
     *
     * @return bool
     */
    private function isChanged(bool $value): bool
    {
        if ($this->isNew()) {
            return true;
        }

        return User::find($this->user->id)->managed_by !== $value;
    }

    /**
     * It's an insert
     *
     * @return bool
     */
    private function isNew(): bool
    {
        return ! $this->user;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You can only change :attribute to groups you are admin of.';
    }
}

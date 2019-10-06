<?php

namespace App\Rules;

use App\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserLogoRule implements Rule
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

        if ($this->isNew()) {
            return Auth::user()->canManageLogo($value);
        }

        return $this->user->canUseLogo($value);
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

        return User::find($this->user->id)->logo_id !== $value;
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
        return 'The validation error message.';
    }
}

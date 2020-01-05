<?php


namespace App\Http\Controllers\Auth;

use App\Rules\PasswordRule;
use Spatie\WelcomeNotification\WelcomeController as BaseWelcomeController;

class WelcomeController extends BaseWelcomeController
{
    /**
     * Where to redirect users after the password was set.
     *
     * @var string
     */
    protected $redirectTo = '/';

    public function rules()
    {
        return [
            'password' => ['required', 'confirmed', new PasswordRule()],
        ];
    }
}


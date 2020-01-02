<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\RegistrationReceivedNotification;
use App\Notifications\UserRegisteredNotification;
use App\User;
use App\UserRegistration;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware('throttle:6,1')->only('register');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'city'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'max:170', 'email', 'unique:users,email'],
            'comment'    => ['sometimes', 'string', 'max:1000'],
        ]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $data = new UserRegistration($request->all());

        $this->notifyAdmin($data);

        return view('auth.approval');
    }

    /**
     * Inform the site admin about the login application.
     *
     * @param  UserRegistration  $data
     */
    private function notifyAdmin(UserRegistration $data)
    {
        Notification::route('mail', config('app.admin_email'))
                    ->notify(new UserRegisteredNotification($data));
    }
}

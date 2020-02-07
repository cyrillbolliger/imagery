<?php

namespace App\Http\Controllers;

use App\Group;
use App\Notifications\AccountCreatedNotification;
use App\Rules\ImmutableRule;
use App\Rules\PasswordRule;
use App\Rules\SuperAdminRule;
use App\Rules\UserLogoRule;
use App\Rules\UserManagedByRule;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Collection
     */
    public function index()
    {
        $user = Auth::user();

        return $user->manageableUsers();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @param  User  $managed  the user to update
     *
     * @return Response
     */
    public function store(Request $request, User $managed)
    {
        $data = $request->validate([
            'id'             => ['sometimes', new ImmutableRule($managed)],
            'first_name'     => ['required', 'string', 'max:255'],
            'last_name'      => ['required', 'string', 'max:255'],
            'email'          => ['required', 'max:170', 'email', 'unique:users'],
            'password'       => ['sometimes', new PasswordRule()],
            'added_by'       => ['sometimes', 'in:'.Auth::id()],
            'managed_by'     => ['required', 'exists:groups,id', new UserManagedByRule(null)],
            'default_logo'   => ['nullable', 'exists:logos,id', new UserLogoRule(null)],
            'super_admin'    => ['sometimes', 'required', 'boolean', new SuperAdminRule(null)],
            'lang'           => ['required', Rule::in(\App\User::LANGUAGES)],
            'login_count'    => ['sometimes', new ImmutableRule($managed)],
            'last_login'     => ['sometimes', new ImmutableRule($managed)],
            'remember_token' => ['sometimes', new ImmutableRule($managed)],
            'created_at'     => ['sometimes', new ImmutableRule($managed)],
            'updated_at'     => ['sometimes', new ImmutableRule($managed)],
            'deleted_at'     => ['sometimes', new ImmutableRule($managed)],
        ]);
        $managed->fill($data);

        if ( ! isset($data['password'])) {
            $data['password'] = Str::random(32);
        }

        $managed->password = Hash::make($data['password']);

        if ( ! $managed->save()) {
            return response('Could not save user.', 500);
        }

        return $managed;
    }

    /**
     * Display the specified resource.
     *
     * @param  User  $managed
     *
     * @return User
     */
    public function show(User $managed)
    {
        return $managed;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  User  $managed  the user to update
     *
     * @return User
     */
    public function update(Request $request, User $managed)
    {
        $data = $request->validate([
            'id'             => ['sometimes', new ImmutableRule($managed)],
            'first_name'     => ['sometimes', 'required', 'string', 'max:255'],
            'last_name'      => ['sometimes', 'required', 'string', 'max:255'],
            'email'          => ['sometimes', 'required', 'max:170', 'email', 'unique:users,email,'.$managed->id],
            'password'       => ['sometimes', 'required', new PasswordRule()],
            'added_by'       => ['sometimes', new ImmutableRule($managed)],
            'managed_by'     => ['sometimes', 'required', 'exists:groups,id', new UserManagedByRule($managed)],
            'default_logo'   => ['sometimes', 'nullable', 'exists:logos,id', new UserLogoRule($managed)],
            'super_admin'    => ['sometimes', 'boolean', new SuperAdminRule($managed)],
            'lang'           => ['sometimes', 'required', Rule::in(\App\User::LANGUAGES)],
            'login_count'    => ['sometimes', new ImmutableRule($managed)],
            'last_login'     => ['sometimes', new ImmutableRule($managed)],
            'remember_token' => ['sometimes', new ImmutableRule($managed)],
            'created_at'     => ['sometimes', new ImmutableRule($managed)],
            'updated_at'     => ['sometimes', new ImmutableRule($managed)],
            'deleted_at'     => ['sometimes', new ImmutableRule($managed)],
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        if ( ! $managed->update($data)) {
            return response('Could not save user.', 500);
        }

        return $managed;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User  $managed
     *
     * @return Response
     * @throws Exception
     */
    public function destroy(User $managed)
    {
        if ( ! $managed->delete()) {
            return response('Could not delete user.', 500);
        }

        return response(null, 204);
    }

    /**
     * Logout the current user
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|Response
     */
    public function logout()
    {
        Auth::logout();

        return response(null, 204);
    }

    /**
     * Send an invitation email to the user
     *
     * @param  Request  $request
     * @param  User  $managed
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|Response
     */
    public function invite(Request $request, User $managed)
    {
        $expiresAt = now()->addDays(config('app.onboarding_expiration'));
        $managed->sendWelcomeNotification($expiresAt);

        return response(null, 204);
    }
}

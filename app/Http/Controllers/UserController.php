<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Rules\PasswordRule;
use App\Rules\SuperAdminRule;
use App\Rules\UniqueUpdateRule;
use App\Rules\UserLogoRule;
use App\Rules\UserManagedByRule;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
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
     * @param  User  $user  the user to update
     *
     * @return Response
     */
    public function store(Request $request, User $user)
    {
        $data = $request->validate([
            'first_name'   => 'required',
            'last_name'    => 'required',
            'email'        => ['required', 'max:170', 'email', 'unique:users'],
            'password'     => ['required', new PasswordRule()],
            'managed_by'   => ['required', 'exists:groups,id', new UserManagedByRule(null)],
            'default_logo' => ['nullable', 'exists:logos,id', new UserLogoRule(null)],
            'super_admin'  => ['sometimes', 'boolean', new SuperAdminRule(null)],
            'lang'         => ['required', Rule::in(\App\User::LANGUAGES)]
        ]);
        $user->fill($data);

        if ( ! $user->save()) {
            return response('Could not save user.', 500);
        }

        return $user;
    }

    /**
     * Display the specified resource.
     *
     * @param  User  $user
     *
     * @return User
     */
    public function show(User $user)
    {
        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  User  $user  the user to update
     *
     * @return User
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'first_name'   => 'sometimes|required',
            'last_name'    => 'sometimes|required',
            'email'        => ['sometimes', 'required', 'max:170', 'email', 'unique:users,email,'.$user->id],
            'password'     => ['sometimes', 'required', new PasswordRule()],
            'managed_by'   => ['sometimes', 'required', 'exists:groups,id', new UserManagedByRule($user)],
            'default_logo' => ['sometimes', 'nullable', 'exists:logos,id', new UserLogoRule($user)],
            'super_admin'  => ['sometimes', 'boolean', new SuperAdminRule($user)],
            'lang'         => ['sometimes', 'required', Rule::in(\App\User::LANGUAGES)]
        ]);

        if ( ! $user->update($data)) {
            return response('Could not save user.', 500);
        }

        return $user;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User  $user
     *
     * @return Response
     * @throws Exception
     */
    public function destroy(User $user)
    {
        if ( ! $user->delete()) {
            return response('Could not delete user.', 500);
        }

        return response(null, 204);
    }
}

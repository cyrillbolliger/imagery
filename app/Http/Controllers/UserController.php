<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Rules\PasswordRule;
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
     * @param  UserRequest  $request
     * @param  User  $user  the user to update
     *
     * @return Response
     */
    public function store(UserRequest $request, User $user)
    {
        $data = $request->validated();

        $user->fill($data);
        $user->addedBy()->associate(Auth::user());

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
     * @param  UserRequest  $request
     * @param  User  $user  the user to update
     *
     * @return User
     */
    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();

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

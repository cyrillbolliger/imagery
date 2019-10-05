<?php

namespace App\Http\Controllers;

use App\Rules\PasswordRule;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        return $user->manageableUsers();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     *
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $user;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  User  $user  the user to update
     *
     * @return void
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'first_name'   => 'required',
            'last_name'    => 'required',
            'email'        => 'required|email|unique:users,email,'.$user->id,
            'password'     => ['sometimes', new PasswordRule()],
            'managed_by'   => 'required|exists:users',
            'default_logo' => 'nullable|exists:logos',
            'super_admin'  => Auth::user()->super_admin ? 'required|boolean' : ['required', Rule::in([false])],
            'lang'         => ['required', Rule::in(User::LANGUAGES)],
        ]);

        if ( ! $user->update($data)) {
            return response('Could not save user.', 500);
        }

        return $user;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}

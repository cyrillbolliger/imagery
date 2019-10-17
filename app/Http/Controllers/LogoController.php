<?php

namespace App\Http\Controllers;

use App\Logo;
use App\Rules\ImmutableRule;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        return $user->manageableLogos();
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
     * @param  \App\Logo  $logo
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Logo $logo)
    {
        return $logo;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Logo  $logo
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Logo $logo)
    {
        $data = $request->validate([
            'id'         => ['sometimes', new ImmutableRule($logo)],
            'added_by'   => ['sometimes', new ImmutableRule($logo)],
            'filename'   => ['sometimes', 'required', 'max:192'],
            'name'       => ['sometimes', 'required', 'max:80'],
            'created_at' => ['sometimes', new ImmutableRule($logo)],
            'updated_at' => ['sometimes', new ImmutableRule($logo)],
            'deleted_at' => ['sometimes', new ImmutableRule($logo)],
        ]);

        if ( ! $logo->update($data)) {
            return response('Could not save logo.', 500);
        }

        return $logo;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Logo  $logo
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Logo $logo)
    {
        //
    }

    /**
     * Display the specified resource file.
     *
     * @param  \App\Logo  $logo
     *
     * @return \Illuminate\Http\Response
     */
    public function file(Logo $logo)
    {
        return response($logo->getPath());
    }
}

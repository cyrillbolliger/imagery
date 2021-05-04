<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CrowdinController extends Controller
{
    /**
     * Expose the crowdin credentials
     *
     * @return array
     */
    public function showCredentials()
    {
        return [
            'username' => config('app.crowdin_user'),
            'password' => config('app.crowdin_pass'),
        ];
    }
}

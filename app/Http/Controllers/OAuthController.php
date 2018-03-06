<?php

namespace App\Http\Controllers;

class OAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the OAuth page.
     *
     * @return view
     */
    public function show()
    {
        // return 'Under construction';

        return view('passport.index');
    }
}

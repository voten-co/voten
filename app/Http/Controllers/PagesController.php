<?php

namespace App\Http\Controllers;

use Auth;

class PagesController extends Controller
{
    public function welcome()
    {
        if (!Auth::check()) {
            return view('home');
        }

        return view('welcome');
    }

    /**
     * loads the features page.
     *
     * @return view
     */
    public function features()
    {
        return view('landing');
    }
}

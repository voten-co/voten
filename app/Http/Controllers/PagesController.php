<?php

namespace App\Http\Controllers;

use Auth;

class PagesController extends Controller
{
    public function credits()
    {
        return view('pages.credits');
    }

    public function tos()
    {
        return view('pages.tos');
    }

    public function privacyPolicy()
    {
        return view('pages.privacy-policy');
    }

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

    /**
     * loads the about page
     *
     * @return
     */
    public function about()
    {
    	return view('pages.about');
    }
}

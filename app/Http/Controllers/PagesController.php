<?php

namespace App\Http\Controllers;

use Auth;

class PagesController extends Controller
{
    public function welcome()
    {
        return Auth::check() ? view('welcome') : view('home');
    }
}

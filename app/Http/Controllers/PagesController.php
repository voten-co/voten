<?php

namespace App\Http\Controllers;

use Auth;
use App\Faq;
use App\Category;
use Carbon\Carbon;
use App\Submission;
use App\Http\Requests;
use Illuminate\Http\Request;

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
        if (! Auth::check()) {
            return view('landing');
        }

        return view('welcome');
    }

    /**
     * loads the features page
     *
     * @return view
     */
    public function features()
    {
        return view('landing');
    }
}

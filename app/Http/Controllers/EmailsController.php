<?php

namespace App\Http\Controllers;

use App\Mail\AnnouncementEmail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailsController extends Controller
{
    public function __construct()
    {
        $this->middleware('voten-administrator');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.emails.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        cache(['announcement-email:title' => $request->title], 60 * 24 * 3);
        cache(['announcement-email:heading' => $request->heading], 60 * 24 * 3);
        cache(['announcement-email:body' => $request->body], 60 * 24 * 3);
        cache(['announcement-email:button-text' => $request->button_text], 60 * 24 * 3);
        cache(['announcement-email:button-url' => $request->button_url], 60 * 24 * 3);

        return redirect('/emails/announcement/preview');
    }

    /**
     * Preview cached email.
     *
     * @return AnnouncementEmail
     */
    public function preview()
    {
        $title = cache('announcement-email:title');
        $heading = cache('announcement-email:heading');
        $body = cache('announcement-email:body');
        $button_text = cache('announcement-email:button-text');
        $button_url = cache('announcement-email:button-url');

        return new AnnouncementEmail($title, $heading, $body, Auth::user()->username, $button_text, $button_url);
    }

    /**
     * Sends the announcement email to all users with a confirmed email address.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function send(Request $request)
    {
        if (!confirmPassword($request->password)) {
            session()->flash('warning', "Incorrect Password. What kind of an administrator doesn't remember his password? ");

            return back();
        }

        $users = User::whereConfirmed(1)->get();

        $title = cache('announcement-email:title');
        $heading = cache('announcement-email:heading');
        $body = cache('announcement-email:body');
        $button_text = cache('announcement-email:button-text');
        $button_url = cache('announcement-email:button-url');

        foreach ($users as $user) {
            \Mail::to($user->email)->queue(new AnnouncementEmail($title, $heading, $body, $user->username, $button_text, $button_url));
        }

        session()->flash('status', $users->count().' emails have been queued for sending. Sit tight. ');

        return back();
    }
}

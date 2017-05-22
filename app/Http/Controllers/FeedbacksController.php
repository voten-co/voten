<?php

namespace App\Http\Controllers;

use App\Feedback;
use App\Mail\NewFeedback;
use Illuminate\Http\Request;

class FeedbacksController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * Display a listing of the feedback.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_unless($this->mustBeVotenAdministrator(), 403);

        return Feedback::with('owner')->get();
    }


    /**
     * Store a newly created feedback in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'subject' => 'required',
            'description' => 'required',
        ]);

        $feedback = new Feedback;
        $feedback->subject = $request->subject;
        $feedback->user_id = auth()->user()->id;
        $feedback->description = $request->description;
        $feedback->save();

        \Mail::to("fischersully@gmail.com")->queue(new NewFeedback(auth()->user(), $feedback));

        return response("Feedback submitted", 200);
    }


    /**
     * Remove the specified feedback from storage.
     *
     * @param  int  $id
     */
    public function destroy(Request $request)
    {
        abort_unless($this->mustBeVotenAdministrator(), 403);

        Feedback::findOrFail($request->id)->delete();
    }
}

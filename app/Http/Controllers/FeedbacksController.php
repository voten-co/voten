<?php

namespace App\Http\Controllers;

use App\Feedback;
use App\Http\Resources\FeedbackResource;
use App\Mail\NewFeedback;
use Illuminate\Http\Request;

class FeedbacksController extends Controller
{
    /**
     * Store a newly created feedback in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'subject'     => 'required',
            'description' => 'required',
        ]);

        $feedback = new Feedback();
        $feedback->subject = $request->subject;
        $feedback->user_id = auth()->user()->id;
        $feedback->description = $request->description;
        $feedback->save();

        \Mail::to('fischersully@gmail.com')->queue(new NewFeedback(auth()->user(), $feedback));

        return res(201, 'Feedback submitted');
    }

    public function get(Feedback $feedback)
    {
        return new FeedbackResource($feedback);
    }

    public function index()
    {
        return FeedbackResource::collection(
            Feedback::simplePaginate(20)
        );
    }

    /**
     * Destroy the specified feedback from storage.
     *
     * @param Request $request
     */
    public function destroy($feedback_id)
    {
        Feedback::withTrashed()->findOrFail($feedback_id)->delete();
    }
}

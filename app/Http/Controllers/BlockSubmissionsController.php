<?php

namespace App\Http\Controllers;

use App\Traits\CachableUser;
use Auth;
use DB;
use Illuminate\Http\Request;
use App\Submission;

class BlockSubmissionsController extends Controller
{
    use CachableUser;

    /**
     * Store a newly created hidden_submission in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Submission $submission)
    {
        try {
            DB::table('hides')->insert([
                'user_id'       => Auth::id(),
                'submission_id' => $submission->id,
            ]);

            // update the cache records for hiddenSubmissions:
            $this->updateHiddenSubmissions(Auth::id(), $submission->id);

            return res(201, 'Submission is now hidden.');
        } catch (\Exception $e) {
            return res(500, 'Something went wrong!');
        }
    }
    
    /**
     * Store a newly created hidden_submission in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Submission $submission)
    {
        try {
            DB::table('hides')->where([
                ['user_id', Auth::id()],
                ['submission_id', $submission->id],
            ])->delete();

            // update the cache records for hiddenSubmissions:
            $this->updateHiddenSubmissions(Auth::id(), $submission->id);

            return res(200, 'Submission is no longer hidden.');
        } catch (\Exception $e) {
            return res(500, 'Something went wrong!');
        }
    }
}

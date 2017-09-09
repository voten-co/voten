<?php

namespace App\Http\Controllers;

use App\Traits\CachableUser;
use Auth;
use DB;
use Illuminate\Http\Request;

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
    public function store(Request $request)
    {
        $request->validate([
            'submission_id' => 'required|integer',
        ]);

        try {
            DB::table('hides')->insert([
                'user_id'       => Auth::id(),
                'submission_id' => $request->submission_id,
            ]);

            // update the cach record for hiddenSubmissions:
            $this->updateHiddenSubmissions(Auth::id(), $request->submission_id);

            return response('Submission blocked successfully.', 200);
        } catch (\Exception $e) {
            return response('Something went wrong!', 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Filters;
use App\Submission;
use App\Traits\CachableUser;
use App\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    use Filters, CachableUser;

    /**
     * Searches through the database for the model with containing the keyword.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'type'     => 'required',
            'searched' => 'required',
        ]);

        try {
            if ($request->type == 'Channels') {
                return Channel::search($request->searched)->take(20)->get();
            }

            if ($request->type == 'Submissions') {
                return $this->sugarFilter(Submission::search($request->searched)->take(20)->get());
            }

            if ($request->type == 'Users') {
                return User::search($request->searched)->take(20)->get();
            }
        } catch (\Exception $exception) {
            app('sentry')->captureException($exception);

            return [];
        }
    }
}

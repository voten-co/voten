<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Filters;
use App\Http\Resources\ChannelResource;
use App\Http\Resources\SubmissionResource;
use App\Http\Resources\UserResource;
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
            'type'     => 'required|in:Channels,Submissions,Users,channels,submissions,users',
            'keyword'  => 'required|string',
        ]);

        try {
            if (strtolower($request->type) == 'channels') {
                return ChannelResource::collection(
                    Channel::search($request->keyword)->paginate(20)
                );
            }

            if (strtolower($request->type) == 'submissions') {
                return SubmissionResource::collection(
                    $this->sugarFilter(
                        Submission::search($request->keyword)->paginate(20)
                    )
                );
            }

            if (strtolower($request->type) == 'users') {
                return UserResource::collection(
                    User::search($request->keyword)->paginate(20)
                );
            }
        } catch (\Exception $exception) {
            app('sentry')->captureException($exception);

            return res(500, 'Oops, something went wrong.');
        }
    }

    /**
     * @param \Illuminate\Http\Request
     *
     * @return \Illuminate\Support\Collection
     */
    public function conversations(Request $request)
    {
        $this->validate($request, [
            'keyword' => 'required|string',
        ]);

        return UserResource::collection(
            $this->UsersFilter(
                $this->noAlreadyContact(
                    User::search($request->keyword)->take(30)->get()
                )
            )
        );
    }
}

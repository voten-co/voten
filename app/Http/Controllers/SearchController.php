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

        switch (strtolower($request->input('type', 'channels'))) {
            case 'channels':
                return ChannelResource::collection(
                    Channel::search($request->keyword)->paginate(20)
                );
                break;
            
            case 'submissions':
                return SubmissionResource::collection(
                    $this->sugarFilter(
                        Submission::search($request->keyword)->paginate(20)
                    )
                );
                break;
            
            case 'users':
                return UserResource::collection(
                    User::search($request->keyword)->paginate(20)
                );
                break;
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

<?php

namespace App\Http\Controllers;

use App\HelpVote;
use Illuminate\Http\Request;

class HelpVotesController extends Controller
{
    /**
     * Upvote on help article.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function upVote(Request $request)
    {
        $request->validate([
            'help_id' => 'required|integer|exists:helps,id',
        ]);

        try {
            HelpVote::create([
                'help_id'    => $request->help_id,
                'ip_address' => getRequestIpAddress(),
                'type'       => 'upvote',
            ]);
        } catch (\Exception $e) {
            return response('Successfully voted on help article.', 200);
        }
    }

    /**
     * Downvote on help article.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function downVote(Request $request)
    {
        $request->validate([
            'help_id' => 'required|integer|exists:helps,id',
        ]);

        try {
            HelpVote::create([
                'help_id'    => $request->help_id,
                'ip_address' => getRequestIpAddress(),
                'type'       => 'downvote',
            ]);
        } catch (\Exception $e) {
            return response('Successfully voted on help article.', 200);
        }
    }
}

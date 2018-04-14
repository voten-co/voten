<?php

namespace App\Http\Controllers;

use App\Events\ConversationRead;
use App\Http\Resources\ConversationResource;
use App\Rules\NotSelfId;
use Auth;
use DB;
use Illuminate\Http\Request;
use App\Traits\CachableUser;
use App\User;

class ConversationsController extends Controller
{
    use CachableUser;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * all the conversations auth user has ever had.
     *
     * @return \Illuminate\Support\Collection
     */
    public function index()
    {
        return ConversationResource::collection(
            Auth::user()->contacts
        );
    }

    /**
     * The receiver has opened the conversation, so let's broadcast "ConversationRead".
     *
     * @param User $user
     *
     * @return Response
     */
    public function broadcastConversationAsRead(User $user)
    {
        event(new ConversationRead($user->id, Auth::id()));

        return res(200, '"seen" event broadcasted successfully. ');
    }

    /**
     * deletes all the messages in the conversation.
     *
     * @return response
     */
    public function destroy(User $user)
    {
        DB::table('conversations')->where([
            'user_id'    => Auth::id(),
            'contact_id' => $user->id,
        ])->delete();

        return res(200, 'left conversation successfully');
    }
}

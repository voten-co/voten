<?php

namespace App\Http\Controllers;

use App\Events\ConversationRead;
use App\Http\Resources\ConversationResource;
use App\Rules\NotSelfId;
use App\Traits\CachableUser;
use Auth;
use DB;
use Illuminate\Http\Request;

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
     * The reciever has opened the conversation, so let's broadcast "ConversationRead".
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Response
     */
    public function broadcastConversaionAsRead(Request $request)
    {
        $this->validate($request, [
            'user_id' => ['required', 'integer', new NotSelfId(), 'exists:users,id'],
        ]);

        event(new ConversationRead($request->user_id, Auth::id()));

        return res(200, '"seen" event broadcasted successfully. ');
    }

    /**
     * deletes all the messages in the conversation.
     *
     * @return response
     */
    public function destroy(Request $request)
    {
        $this->validate($request, [
            'user_id' => ['required', 'integer', new NotSelfId(), 'exists:users,id'],
        ]);

        DB::table('conversations')->where([
            'user_id'    => Auth::id(),
            'contact_id' => $request->user_id,
        ])->delete();

        return res(200, 'left conversation successfully');
    }

    /**
     * toggles contact to the blockedUsers list.
     *
     * @return response
     */
    public function block(Request $request)
    {
        $this->validate($request, [
            'user_id' => ['required', 'integer', new NotSelfId()],
        ]);

        $user = Auth::user();

        $result = $user->hiddenUsers()->toggle($request->user_id);

        // subscibed
        if ($result['attached']) {
            $this->updateBlockedUsers($user->id, $request->user_id, true);

            return res(200, 'User blocked.');
        }

        // unsubscribed
        $this->updateBlockedUsers($user->id, $request->user_id, false);

        return res(200, 'User unblocked. ');
    }
}

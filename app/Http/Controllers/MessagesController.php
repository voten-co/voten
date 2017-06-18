<?php

namespace App\Http\Controllers;

use App\Events\ConversationRead;
use App\Events\MessageCreated;
use App\Events\MessageRead;
use App\Filters;
use App\Message;
use App\Traits\CachableUser;
use App\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class MessagesController extends Controller
{
    use Filters, CachableUser;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Stores the message model.
     *
     * @param Illuminate\Http\Request
     *
     * @return Illuminate\Support\Collection
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'text'    => 'required',
            'contact' => 'required|integer',
        ]);

        if ($request->contact == Auth::user()->id) {
            return response("You can't send a message to yourself. Is everything alright buddy?!", 500);
        }

        $message = Auth::user()->messages()->create([
            'data' => array_only($request->all(), ['text']),
        ]);

        Auth::user()->conversations()->attach($message, [
            'contact_id' => $request->contact,
        ]);

        if (!$this->isAuthUserBlockedToContact($request->contact, Auth::user()->id)) {
            User::find($request->contact)->conversations()->attach($message, [
                'contact_id' => Auth::user()->id,
            ]);

            // broadcast the message to the other person
            event(new MessageCreated($message, $request->contact, Auth::user()->id));
        }

        $message->owner = Auth::user();

        return $message;
    }

    /**
     * is the auth user in the contact's blockedUsers list.
     *
     * @param int $contact_id
     * @param int $auth_user_id
     *
     * @return bool
     */
    protected function isAuthUserBlockedToContact($contact_id, $auth_user_id)
    {
        if (Auth::user()->isShadowBanned()) {
            return true;
        }

        $list = collect($this->blockedUsers($contact_id));

        return $list->contains($auth_user_id);
    }

    /**
     * @param Illuminate\Http\Request
     *
     * @return Illuminate\Support\Collection
     */
    public function getMessages(Request $request)
    {
        $this->validate($request, [
            'contact_id' => 'required|integer',
            'page'       => 'required|integer',
        ]);

        $messages = Auth::user()->conversations()
                                ->where('contact_id', $request->contact_id)
                                ->simplePaginate(40);

        $unreads = $messages->filter(function ($value, $key) {
            return $value->read_at == null;
        });

        if (count($unreads) > 0) {
            event(new ConversationRead($request->sender_id, Auth::user()->id));
        }

        $this->markAllAsRead($request->contact_id);

        return $messages;
    }

    /**
     * The reciever has opened the conversation, so let's broadcast "ConversationRead".
     *
     * @param Illuminate\Http\Request $request
     *
     * @return void
     */
    public function broadcastConversaionAsRead(Request $request)
    {
        event(new ConversationRead($request->sender_id, Auth::user()->id));
    }

    /**
     * @param Illuminate\Http\Request $request
     *
     * @return string status
     */
    public function destroyMessages(Request $request)
    {
        if (count($request->input('messages')) < 1) {
            return responder()->error('error', 500, "No messages are selected :| You're kidding right?!");
        }

        Auth::user()->conversations()->detach($request->input('messages'));

        return count($request->input('messages')).' messages were deleted.';
    }

    /**
     * deletes all the messages in the conversation.
     *
     * @return response
     */
    public function leaveConversation(Request $request)
    {
        $this->validate($request, [
            'contact_id' => 'required|integer',
        ]);

        DB::table('conversations')->where([
            'user_id'    => Auth::user()->id,
            'contact_id' => $request->contact_id,
        ])->delete();

        return response('left conversation successfully', 200);
    }

    /**
     * toggles contact to the blockedUsers list.
     *
     * @return response
     */
    public function blockUser(Request $request)
    {
        $this->validate($request, [
            'contact_id' => 'required|integer',
        ]);

        $user = Auth::user();

        $result = $user->hiddenUsers()->toggle($request->contact_id);

        // subscibed
        if ($result['attached']) {
            $this->updateBlockedUsers($user->id, $request->contact_id, true);

            return response('blocked', 200);
        }

        // unsubscribed
        $this->updateBlockedUsers($user->id, $request->contact_id, false);

        return response('unblocked', 200);
    }

    /**
     * all the conversations auth user has ever had.
     *
     * @return Illuminate\Support\Collection
     */
    public function getContacts()
    {
        return Auth::user()->contacts;
    }

    /**
     * @param Illuminate\Http\Request
     *
     * @return Illuminate\Support\Collection
     */
    public function searchContact(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|String',
        ]);

        return $this->UsersFilter($this->noAlreadyContact(User::where('username', 'like', '%'.$request->username.'%')
                    ->where('username', '!=', Auth::user()->username)
                    ->select('id', 'username', 'name', 'avatar')->take(30)->get()));
    }

    /**
     * marks all conversation's messages as read.
     *
     * @param int $contact_id
     *
     * @return void
     */
    protected function markAllAsRead($contact_id)
    {
        Auth::user()->conversations()->where('contact_id', $contact_id)->get()->map(function ($m) {
            $m->update(['read_at' => Carbon::now()]);
        });
    }

    /**
     * marks a single message as read.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return (string) status
     */
    public function markAsRead(Request $request)
    {
        $this->validate($request, [
            'message_id' => 'required|integer',
            'sender_id'  => 'required|integer',
        ]);

        event(new MessageRead($request->message_id, $request->sender_id, Auth::user()->id));

        Message::find($request->message_id)->update(['read_at' => Carbon::now()]);

        return 'message was read';
    }

    /**
     * Returns the required user's info for displaying him as a contact.
     *
     * @return Collection
     */
    public function contactInfo(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|integer',
        ]);

        return User::select('avatar', 'id', 'name', 'username')->findOrFail($request->user_id);
    }
}

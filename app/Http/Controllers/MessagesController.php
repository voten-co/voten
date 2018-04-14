<?php

namespace App\Http\Controllers;

use App\Events\ConversationRead;
use App\Events\MessageCreated;
use App\Events\MessageRead;
use App\Filters;
use App\Http\Resources\MessageResource;
use App\Message;
use App\Rules\NotSelfId;
use App\Traits\CachableUser;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * @param \Illuminate\Http\Request
     *
     * @return \Illuminate\Support\Collection
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'body' => 'required|max:5000',
            'user_id' => ['required', new NotSelfId(), 'exists:users,id'],
        ]);

        $message = Auth::user()->messages()->create([
            'data' => [
                'text' => request('body'),
            ],
        ]);

        Auth::user()->conversations()->attach($message, [
            'contact_id' => $request->user_id,
        ]);

        if (!$this->isAuthUserBlockedToContact($request->user_id, Auth::user()->id)) {
            User::find($request->user_id)->conversations()->attach($message, [
                'contact_id' => Auth::id(),
            ]);

            // broadcast the message to the other person
            event(new MessageCreated($message, $request->user_id, Auth::user()->id));
        }

        $message->owner = Auth::user();

        return new MessageResource($message);
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
        $list = collect($this->blockedUsers($contact_id));

        return $list->contains($auth_user_id);
    }

    /**
     * @param \Illuminate\Http\Request
     *
     * @return \Illuminate\Support\Collection
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'contact_id' => ['required', 'exists:users,id', new NotSelfId],
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

        return MessageResource::collection($messages);
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return Response
     */
    public function destroy(Message $message)
    {
        Auth::user()->conversations()->detach($message->id);

        return res(200, ' message deleted successfully.');
    }
    
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return Response
     */
    public function batchDestroy(Request $request)
    {
        $request->validate([
            'messages' => 'array|min:1',
        ]);

        Auth::user()->conversations()->detach($request->input('messages'));

        return res(200, count($request->input('messages')) . ' messages were deleted.');
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
     * @param \Illuminate\Http\Request $request
     *
     * @return (string) status
     */
    public function markAsRead(Message $message)
    {
        abort_unless(
            Auth::user()->conversations()->where([
                ['contact_id', $message->user_id],
                ['message_id', $message->id]
            ])->exists()
            , 403
        );

        $message->update([
            'read_at' => now(),
        ]);

        event(new MessageRead($message));

        return res(200, 'Message marked as read successfully.');
    }
}

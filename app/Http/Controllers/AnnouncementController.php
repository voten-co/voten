<?php

namespace App\Http\Controllers;

use App\Announcement;
use App\Traits\CachableChannel;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    use CachableChannel;

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['get']]);
    }

    /**
     * Shows the announcements page in backend(admin panel).
     *
     * @return view
     */
    public function show()
    {
        abort_unless($this->mustBeVotenAdministrator(), 403);

        $announcements = Announcement::where([
            ['channel_name', 'home'],
        ])->get();

        return view('backend.announcements', compact('announcements'));
    }

    /**
     * Stores a Announcement record.
     *
     * @param \Illuminate\Support\Request $request
     *
     * @return void
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'body'    => 'required',
            'title'   => 'required',
        ]);

        if ($request->channel_name == 'home' && !$request->ajax()) {
            // only a voten administrator is able to make an announcement to everyone's home-feed
            abort_unless($this->mustBeVotenAdministrator(), 403);
        } else {
            $channel = $this->getChannelByName($request->channel);
            abort_unless($this->mustBeAdministrator($channel->id), 403);
        }

        // active duration
        if (!$request->duration || $request->duration == 0) {
            $active_until = Carbon::now()->addYears(17);
        } else {
            $active_until = Carbon::now()->addDays($request->duration);
        }

        $announcement = new Announcement([
            'channel_name'  => $request->channel_name,
            'user_id'       => Auth::user()->id,
            'title'         => $request->title,
            'body'          => $request->body,
            'active_until'  => $active_until,
        ]);
        $announcement->save();

        return $request->ajax() ? $announcement : back();
    }

    /**
     * Get the annoucnement record.
     *
     * @param \Illuminate\Support\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function get(Request $request)
    {
        // let's not show any announcements to guests (at least for now)
        if (!Auth::check()) {
            return [];
        }

        return Announcement::where([
            ['channel_name', 'home'],
            ['active_until', '>=', Carbon::now()],
        ])->whereNotIn('id', Auth::user()->seenAnnouncements())->get();
    }

    /**
     * Marks the announcement as seen.
     *
     * @param \Illuminate\Support\Request $request
     *
     * @return response
     */
    public function seen(Request $request)
    {
        $this->validate($request, [
            'announcement_id' => 'required|integer',
        ]);

        DB::table('seen_announcements')->insert([
            'user_id'         => Auth::user()->id,
            'announcement_id' => $request->announcement_id,
        ]);

        return response('Announcement has been marked as seen.', 200);
    }

    /**
     * destroys an Announcement record.
     *
     * @return redirect
     */
    public function destroy(Announcement $announcement, Request $request)
    {
        if ($announcement->channel_name == 'home') {
            abort_unless($this->mustBeVotenAdministrator(), 403);
        } else {
            $channel = $this->getChannelByName($announcement->channel_name);
            abort_unless($this->mustBeAdministrator($channel->id), 403);
        }

        $announcement->delete();

        return $request->ajax() ? response('Announcement has beeen deleted successfully.') : back();
    }
}

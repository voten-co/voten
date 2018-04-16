<?php

namespace App\Http\Controllers;

use App\Announcement;
use App\Traits\CachableChannel;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use App\Http\Resources\AnnouncementResource;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    use CachableChannel;

    public function __construct()
    {
        $this->middleware('voten-administrator', ['except' => ['get', 'seen']]);
    }

    /**
     * Shows the announcements page in backend(admin panel).
     *
     * @return view
     */
    public function show()
    {
        $announcements = Announcement::all();

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
            'duration'   => 'required|integer|min:0',
        ]);

        return Announcement::create([
            'user_id'       => Auth::id(),
            'title'         => $request->title,
            'body'          => $request->body,
            'active_until'  => Carbon::now()->addDays($request->duration),
        ]);
    }

    /**
     * Get auth user's unseen announcements.
     *
     * @return AnnouncementResource
     */
    public function get()
    {
        return AnnouncementResource::collection(
            Announcement::where('active_until', '>=', Carbon::now())->whereNotIn('id', Auth::user()->seenAnnouncements())->get()
        );
    }

    /**
     * Marks the announcement as seen.
     *
     * @param \Illuminate\Support\Request $request
     *
     * @return response
     */
    public function seen(Announcement $announcement)
    {
        try {
            DB::table('seen_announcements')->insert([
                'user_id'         => Auth::id(),
                'announcement_id' => $announcement->id,
            ]);
        } catch (\Exception $exception) {}
            
        return res(200, 'Announcement has been marked as seen.');
    }

    /**
     * destroys an Announcement record.
     *
     * @return redirect
     */
    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return res(200, 'Announcement has been deleted successfully.');
    }
}

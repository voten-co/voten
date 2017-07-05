<?php

namespace App\Http\Controllers;

use App\Announcement;
use App\Traits\CachableCategory;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    use CachableCategory;

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
            ['category_name', 'home'],
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

        if ($request->category_name == 'home' && !$request->ajax()) {
            // only a voten administrator is able to make an announcement to everyone's home-feed
            abort_unless($this->mustBeVotenAdministrator(), 403);
        } else {
            $category = $this->getCategoryByName($request->category);
            abort_unless($this->mustBeAdministrator($category->id), 403);
        }

        // active duration
        if (!$request->duration || $request->duration == 0) {
            $active_until = Carbon::now()->addYears(17);
        } else {
            $active_until = Carbon::now()->addDays($request->duration);
        }

        $announcement = new Announcement([
            'category_name' => $request->category_name,
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
            ['category_name', 'home'],
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
        if ($announcement->category_name == 'home') {
            abort_unless($this->mustBeVotenAdministrator(), 403);
        } else {
            $category = $this->getCategoryByName($announcement->category_name);
            abort_unless($this->mustBeAdministrator($category->id), 403);
        }

        $announcement->delete();

        return $request->ajax() ? response('Announcement has beeen deleted successfully.') : back();
    }
}

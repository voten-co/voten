<?php

namespace App\Http\Controllers;

use App\Ban;
use App\User;
use App\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BanController extends Controller
{
    /**
     * Stores a record on the database to ban the user.
     *
     * @param Illuminate\Http\Request $request
     * @return response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'category' => 'alpha_num|max:25',
            'duration' => 'integer|min:0|max:999'
        ]);

        if ($request->category != 'all') {
            $category = Category::where('name', $request->category)->firstOrFail();
        }

        // make sure only voten-administrators are able to ban users everywhere
        if ($request->category == 'all') {
            abort_unless($this->mustBeVotenAdministrator(), 403);
        } else {
            abort_unless($this->mustBeModerator($category->id), 403);
        }

        $user = User::where('username', $request->username)->firstOrFail();

        // BAN DURATION: if the duration is set as 0 we set a really big number like 17 years!
        if (!$request->duration || $request->duration == 0) {
            $unban_at = Carbon::now()->addYears(17);
        } else {
            $unban_at = Carbon::now()->addDays($request->duration);
        }

        $blockedUser = new Ban([
            "user_id" => $user->id,
            "category" => $request->category,
            "description" => $request->description,
            "unban_at" => $unban_at
        ]);
        $blockedUser->save();

        $blockedUser->user = $user;

        return $blockedUser;
    }


    /**
     * Returns all the users that are banned from submitting to this category
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Support\Collection
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'category' => 'required|max:25'
        ]);

        if ($request->category != 'all') {
            $category = Category::where('name', $request->category)->firstOrFail();
        }

        // make sure only voten-administrators are able to ban users everywhere
        if ($request->category == 'all') {
            abort_unless($this->mustBeVotenAdministrator(), 403);
        } else {
            abort_unless($this->mustBeModerator($category->id), 403);
        }

        return Ban::where('category', $request->category)
                    ->with('user')
                    ->where('unban_at', '>=', Carbon::now())
                    ->orderBy('created_at', 'desc')
                    ->get();
    }


    /**
     * Unban
     *
     * @param Illuminate\Http\Request $request
     * @return response
     */
    public function destroy(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|integer',
            'category' => 'alpha_num|max:25'
        ]);

        if ($request->category != 'all') {
            $category = Category::where('name', $request->category)->firstOrFail();
        }

        // make sure only voten-administrators are able to ban users everywhere
        if ($request->category == 'all') {
            abort_unless($this->mustBeVotenAdministrator(), 403);
        } else {
            abort_unless($this->mustBeModerator($category->id), 403);
        }

        Ban::where('user_id', $request->user_id)
                    ->where('category', $request->category)
                    ->delete();

        return response("Unbanned from " . $request->category, 200);
    }
}

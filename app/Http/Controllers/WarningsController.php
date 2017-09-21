<?php

namespace App\Http\Controllers;

use App\Category;
use App\Mail\CategoryRemovalWarning;
use Carbon\Carbon;

class WarningsController extends Controller
{
    public function __construct()
    {
        $this->middleware('administrator');
    }

    /**
     * Sends "CategoryRemovalWarning" email for inactive categories.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function categoriesRemoval()
    {
        $inactive_categories = Category::where('created_at', '<=', Carbon::now()->subMonths(2))
            ->whereDoesntHave('submissions', function ($query) {
                $query->where('created_at', '>=', Carbon::now()->subMonths(2));
            })->get();

        foreach ($inactive_categories as $category) {
            $mods = $category->moderators;

            foreach ($mods as $user) {
                if ($user->confirmed) {
                    \Mail::to($user->email)->queue(new CategoryRemovalWarning($user, $category));
                }
            }
        }

        session()->flash('status', $inactive_categories->count().' channels are going to get a warning email. ');

        return back();
    }
}

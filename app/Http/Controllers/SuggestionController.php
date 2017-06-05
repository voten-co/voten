<?php

namespace App\Http\Controllers;

use App\Category;
use App\Suggested;
use App\Traits\CachableCategory;
use App\Traits\CachableUser;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SuggestionController extends Controller
{
    use CachableUser, CachableCategory;

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['category']]);
    }

    /**
     * Returns the suggested category.
     *
     * @return Illuminate\Support\Collection $category
     */
    public function category()
    {
        try {
        	if (Auth::check()) {
        		return Suggested::whereNotIn('category_id', $this->subscriptions())->inRandomOrder()->firstOrFail()->category;
        	}

        	return Suggested::inRandomOrder()->firstOrFail()->category;
        } catch (\Exception $e) {
            return 'null';
        }
    }

    /**
     * Returnes a collection of suggested categories for the auth user.
     *
     * @param Illuminate\Support\Request $request
     *
     * @return Illuminate\Support\Collection
     */
    public function findCategories(Request $request)
    {
        return Suggested::whereNotIn('category_id', $this->subscriptions())->orderBy('z_index', 'desc')->simplePaginate(20);
    }

    /**
     * stores a new suggested category record.
     *
     * @param Illuminate\Support\Request $request
     *
     * @return Illuminate\Support\Collection
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'category_name' => 'required',
            'z_index'       => 'required|integer',
        ]);

        abort_unless($this->mustBeVotenAdministrator(), 403);

        $category = $this->getCategoryByName($request->category_name);

        $suggested = new Suggested([
            'z_index'     => $request->z_index,
            'group'       => $request->group,
            'category_id' => $category->id,
        ]);

        $suggested->save();

        Cache::forget('default-categories-ids');

        return Suggested::findOrFail($suggested->id);
    }

    /**
     * indexes all the models for admin panel.
     *
     * @return Illuminate\Support\Collection
     */
    public function adminIndex()
    {
        abort_unless($this->mustBeVotenAdministrator(), 403);

        return Suggested::all();
    }

    /**
     * destroys the record.
     *
     * @return response
     */
    public function destroy(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
        ]);

        abort_unless($this->mustBeVotenAdministrator(), 403);

        Suggested::findOrFail($request->id)->delete();

        Cache::forget('default-categories-ids');

        return response('Channel is no longer suggested', 200);
    }
}

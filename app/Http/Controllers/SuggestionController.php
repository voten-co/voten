<?php

namespace App\Http\Controllers;

use App\Category;
use App\Filters;
use App\Suggested;
use App\Traits\CachableCategory;
use App\Traits\CachableUser;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SuggestionController extends Controller
{
    use CachableUser, CachableCategory, Filters;

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['category']]);
    }

    /**
     * Returns the suggested category.
     *
     * @return \Illuminate\Support\Collection $category
     */
    public function category()
    {
        try {
            if (Auth::check()) {
                return Suggested::whereNotIn('category_id', $this->subscriptions())->inRandomOrder()->firstOrFail()->category;
            }

            return Suggested::where('z_index', '>', 6)->inRandomOrder()->firstOrFail()->category;
        } catch (\Exception $e) {
            return 'null';
        }
    }

    /**
     * Returnes a collection of suggested categories for the auth user.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function findCategories(Request $request)
    {
        // default
        if (!$request->filter && !$request->order_by) {
            $defaultCategories = ($request->exclude_subscribeds == 'true') ? Suggested::whereNotIn('category_id', $this->subscriptions())
                ->orderBy('z_index', 'desc')->simplePaginate(20) : Suggested::orderBy('z_index', 'desc')->simplePaginate(20);

            $defaultCategories->setCollection($defaultCategories->pluck('category'));

            return $defaultCategories;
        }

        // searched
        if ($request->filter) {
            $categories = Category::search($request->filter)->take(20)->get();

            return ($request->exclude_subscribeds == 'true') ? $this->noSubscribedFilter($categories) : $categories;
        }

        // sorted by an option
        $categories = (new Category())->newQuery();

        if ($request->order_by == 'new') {
            $categories->orderBy('id', 'desc');
        } elseif ($request->order_by == 'subscribers') {
            $categories->orderBy('subscribers', 'desc');
        } elseif ($request->order_by == 'activity') {
            $categories->withCount('submissions')->orderBy('submissions_count', 'desc');
        }

        if ($request->exclude_subscribeds == 'true') {
            $categories->whereNotIn('id', $this->subscriptions());
        }

        return $categories->simplePaginate(20);
    }

    /**
     * stores a new suggested category record.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
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
     * @return \Illuminate\Support\Collection
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

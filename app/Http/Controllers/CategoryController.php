<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Category;
use App\CategoryForbiddenName;
use App\Filters;
use App\Submission;
use App\Traits\CachableCategory;
use App\Traits\CachableUser;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use Filters, CachableUser, CachableCategory;

    /**
     * makes sure the user is logged in.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['submissionsAPI', 'getCategory', 'moderators']]);
    }

    /**
     * Returns all the nesseccary information to fill the categoryStore on front-end.
     *
     * @return Collection
     */
    public function fillStore(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $category = $this->getCategoryByName($request->name);

        $category->stats = $this->categoryStats($category->id);

        return $category;
    }

    /**
     * Creates a new category.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return string $name
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'        => 'required|alpha_num|min:3|max:50|unique:categories',
            'description' => 'required|min:10|max:250',
        ]);

        $user = Auth::user();

        $tooEarly = $this->tooEarlyToCreate();

        if ($tooEarly != false) {
            return response("Looks like you're over doing it. You can create another channel in ".$tooEarly.' seconds. Thank you for being patient.', 500);
        }

        if ($this->isForbiddenName(str_slug($request->name, ''))) {
            return response('This name is forbidden. Please pick another one.', 500);
        }

        $category = Category::create([
            'name'        => str_slug($request->name, ''),
            'description' => $request->description,
            'avatar'      => '/imgs/channel-avatar.png',
        ]);

        // subscribes user to category that was just created
        $user->subscriptions()->toggle($category->id);
        $this->updateSubscriptions($user->id, $category->id, true);

        // Set the creator of category as the administrator of it
        $user->categoryRoles()->attach($category, [
            'role' => 'administrator',
        ]);

        return $category;
    }

    /**
     * is the name in the blacklist names.
     *
     * @return bool
     */
    protected function isForbiddenName($name)
    {
        return CategoryForbiddenName::where('name', $name)->exists();
    }

    /**
     * Whether or the user is breaking the time limit for creating another category.
     *
     * @return mixed
     */
    protected function tooEarlyToCreate()
    {
        // exclude white-listed users form this checking
        if ($this->mustBeWhitelisted()) {
            return false;
        }

        $lastCreated = Activity::where([
            ['subject_type', 'App\Category'],
            ['user_id', Auth::user()->id],
            ['name', 'created_category'],
        ])->orderBy('created_at', 'desc')->first();

        if ($lastCreated) {
            $timeDiff = time() - strtotime($lastCreated->created_at);
            // 43200 = 12 hours
            if ($timeDiff < 43200) {
                return 43200 - $timeDiff;
            }
        }

        return false;
    }

    /**
     * Patches the category model with recently send info.
     *
     * @param Illuminate\Support\Request $request
     *
     * @return response
     */
    public function patch(Request $request)
    {
        $this->validate($request, [
            'name'        => 'required|alpha_num|max:25',
            'description' => 'required|max:230',
            'color'       => 'required|in:Dark Blue,Blue,Red,Dark,Pink,Dark Green,Bright Green,Purple,Gray,Orange',
        ]);

        $category = $this->getCategoryByName($request->name);

        abort_unless($this->mustBeAdministrator($category->id), 403);

        $category->update([
            'description' => $request->description,
            'color'       => $request->color,
            'nsfw'        => ($request->nsfw ? true : false),
        ]);

        // it's better to move this to the CategoryWasUpdated event later
        $this->putCategoryInTheCache($category);
        // end

        return response('The channel has been successfully updated', 200);
    }

    /**
     * Searches categories. Mostly used for submiting new submissions.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return Illuminate\Support\Collection
     */
    public function getCategories(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|alpha_num|max:25',
        ]);

        return Category::where('name', 'like', '%'.$request->name.'%')
                    ->orderBy('subscribers', 'desc')
                    ->select('name')->take(100)->get()->pluck('name');
    }

    /**
     * looks for the category by its name.
     *
     * @param Request $request [name]
     *
     * @return [Collection] category
     */
    public function getCategory(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        return $this->getCategoryByName($request->name);
    }

    /**
     * Get submissions API with ajax calls.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return Illuminate\Support\Collection
     */
    public function submissionsAPI(Request $request)
    {
        $this->validate($request, [
            'sort'     => 'alpha_num|max:25',
            'page'     => 'Integer',
            'category' => 'required|alpha_num|max:25',
        ]);

        $submissions = (new Submission())->newQuery();

        $submissions->where('category_name', $request->category);

        // exclude user's hidden submissions
        if (Auth::check()) {
            $submissions->whereNotIn('id', $this->hiddenSubmissions());
        }

        // exclude NSFW if user doens't want to see them or if the user is not authinticated
        if (!Auth::check() || Auth::user()->settings['nsfw'] == false) {
            $submissions->where('nsfw', false);
        }

        if ($request->sort == 'new') {
            $submissions->orderBy('created_at', 'desc');
        }

        if ($request->sort == 'rising') {
            $submissions->where('created_at', '>=', Carbon::now()->subHour())
                        ->orderBy('rate', 'desc');
        }

        if ($request->sort == 'hot') {
            $submissions->orderBy('rate', 'desc');
        }

        return $submissions->simplePaginate(10);
    }

    /**
     * @param App\Category $category
     *
     * @return bool
     */
    protected function isNSWF($category)
    {
        return $category->nsfw == 1 && Auth::user()->settings['nsfw'] == 0;
    }

    /**
     * returns all the user models moderating the category.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return Illuminate\Support\Collection
     */
    public function moderators(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $category = $this->getCategoryByName($request->name);

        return $category->moderators;
    }
}

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
        $this->middleware('auth', ['except' => ['show', 'submissions', 'getCategory', 'moderators', 'fillStore', 'redirect']]);
    }

    /**
     * gets submissions.
     *
     * @param string $category
     *
     * @return Illuminate\Support\Collection
     */
    protected function getSubmissions($category, $sort)
    {
        $submissions = (new Submission())->newQuery();

        $submissions->where('category_name', $category);

        // exclude user's hidden submissions
        if (Auth::check()) {
            $submissions->whereNotIn('id', $this->hiddenSubmissions());
        }

        // exclude NSFW if user doens't want to see them or if the user is not authinticated
        if (!Auth::check() || !settings('nsfw')) {
            $submissions->where('nsfw', false);
        }

        if ($sort == 'new') {
            $submissions->orderBy('created_at', 'desc');
        }

        if ($sort == 'rising') {
            $submissions->where('created_at', '>=', Carbon::now()->subHour())
                        ->orderBy('rate', 'desc');
        }

        if ($sort == 'hot') {
            $submissions->orderBy('rate', 'desc');
        }

        return $submissions->simplePaginate(10);
    }

    /**
     * Get submissions API with ajax calls.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return Illuminate\Support\Collection
     */
    public function submissions(Request $request)
    {
        $this->validate($request, [
            'sort'     => 'alpha_num|max:25',
            'page'     => 'Integer',
            'category' => 'required|alpha_num|max:25',
        ]);

        return $this->getSubmissions($request->category, $request->sort);
    }

    /**
     * shows the submission page to guests.
     *
     * @param string $category
     * @param string $slug
     *
     * @return view
     */
    public function show($category, Request $request)
    {
        if (Auth::check()) {
            return view('welcome');
        }

        $submissions = $this->getSubmissions($category, $request->sort ?? 'hot');
        $category = $this->getCategoryByName($category);
        $category->stats = $this->categoryStats($category->id);

        return view('category.show', compact('submissions', 'category'));
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

        if ($user->isShadowBanned()) {
            return response('I hate to break it to you but your account has been banned.', 500);
        }

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

    /**
     * redirects old channel URLs (/c/channel/hot) to the new one (/c/channel). This is just to
     * to prevent dead URLS and also to respect our old users who shared their channels on
     * social media to support us. To them!
     *
     * @return redirect
     */
    public function redirect($category)
    {
        return redirect('/c/'.$category);
    }
}

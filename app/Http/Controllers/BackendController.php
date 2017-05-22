<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\AppointeddUser;
use App\UserForbiddenName;
use Illuminate\Http\Request;
use App\CategoryForbiddenName;
use Illuminate\Support\Facades\Cache;

class BackendController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * indexes the backend page
     *
     * @return view
     */
    public function index()
    {
        abort_unless($this->mustBeVotenAdministrator(), 403);

        $forbiddenUsernames = UserForbiddenName::all();

        $forbiddenCategoryNames = CategoryForbiddenName::all();

        return view('backend.index', compact('forbiddenUsernames', 'forbiddenCategoryNames'));
    }


    /**
     * indexes the backend page
     *
     * @return view
     */
    public function indexAppointedUsers()
    {
        abort_unless($this->mustBeVotenAdministrator(), 403);

        $appointed_users = AppointeddUser::all();

        return view('backend.appointed-users', compact('appointed_users'));
    }



    /**
     * indexes the backend page
     *
     * @return view
     */
    public function serverControls()
    {
        abort_unless($this->mustBeVotenAdministrator(), 403);

        return view('backend.server-controls');
    }


    /**
     * stores a new forbidden model in the database. This when a user is registering or changing his
     * username. We don't want them to use these names.
     *
     * @param  Request $request
     * @return redirect
     */
    public function storeForbiddenUsername(Request $request)
    {
        abort_unless($this->mustBeVotenAdministrator(), 403);

        $this->validate($request, [
            'username' => 'required|min:3|max:25|unique:users|regex:/^[A-Za-z0-9\._]+$/'
        ]);

        UserForbiddenName::create([
            'username' => $request->username
        ]);

        return back();
    }


    /**
     * destroys a forbidden-username model
     *
     * @param \App\UserForbiddenName $forbidden
     * @return redirect
     */
    public function destroyForbiddenUsername(UserForbiddenName $forbidden)
    {
        abort_unless($this->mustBeVotenAdministrator(), 403);

        $forbidden->delete();

        return back();
    }


    /**
     * stores a new forbidden model in the database. This when a user is registering or changing his
     * username. We don't want them to use these names.
     *
     * @param  Request $request
     * @return redirect
     */
    public function storeForbiddenCategoryName(Request $request)
    {
        abort_unless($this->mustBeVotenAdministrator(), 403);

        $this->validate($request, [
            'name' => 'required|unique:categories'
        ]);

        CategoryForbiddenName::create([
            'name' => $request->name
        ]);

        return back();
    }


    /**
     * destroys a forbidden-username model
     *
     * @param \App\CategoryForbiddenName $forbidden
     * @return redirect
     */
    public function destroyForbiddenCategoryName(CategoryForbiddenName $forbidden)
    {
        abort_unless($this->mustBeVotenAdministrator(), 403);

        $forbidden->delete();

        return back();
    }


    /**
     * stores a new AppointedUser record in the databas and cache
     *
     * @param  Request $request
     * @return redirect
     */
    public function storeAppointed(Request $request)
    {
        abort_unless($this->mustBeVotenAdministrator(), 403);

        $this->validate($request, [
            'username' => 'required',
            'appointed_as' => 'in:administrator,moderator,whitelisted',
        ]);

        $user = User::where('username', $request->username)->firstOrFail();

        AppointeddUser::create([
            'user_id' => $user->id,
            'appointed_as' => $request->appointed_as
        ]);

        Cache::forget('general.voten-administrators');
        Cache::forget('general.whitelisted');

        return back();
    }


    /**
     * destroys a AppointedUser record in the databas and cache
     *
     * @param  \App\AppointeddUser $appointed
     * @return redirect
     */
    public function destroyAppointed(AppointeddUser $appointed)
    {
        abort_unless($this->mustBeVotenAdministrator(), 403);

        if (Auth::user()->id == $appointed->user->id) {
            return "I don't think you really mean it.";
        }

        $appointed->delete();

        Cache::forget('general.voten-administrators');
        Cache::forget('general.whitelisted');

        return back();
    }
}

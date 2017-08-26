<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RolesController extends Controller
{
    /**
     * makes sure the user is logged in.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required|String',
            'user_id'     => 'required|String',
            'role'        => 'required|in:administrator,moderator',
        ]);

        abort_unless($this->mustBeAdministrator(), 403);

        $category = Category::findOrFail($request->category_id);
        $user = User::findOrFail($request->user_id);

        $user->categoryRoles()->attach($category, [
            'role' => $request->role,
        ]);

        return $user->username.'is now a '.$request->role.' in '.$category->name;
    }
}

<?php

namespace App\Http\Controllers;

use App\Help;
use Illuminate\Http\Request;

class HelpController extends Controller
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

    /**
     * index the help models.
     *
     * @return \Illuminate\Support\Collection
     */
    public function index()
    {
        return Help::all();
    }

    /**
     * Creates the Help model with submitted info.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'body'  => 'required',
        ]);

        abort_unless($this->mustBeVotenAdministrator(), 403);

        $help = new Help([
            'title' => $request->title,
            'body'  => $request->body,
        ]);

        $help->save();

        return $help;
    }

    /**
     * Patches the Help model.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function patch(Request $request)
    {
        $this->validate($request, [
            'id'    => 'required',
            'title' => 'required',
            'body'  => 'required',
        ]);

        abort_unless($this->mustBeVotenAdministrator(), 403);

        $help = Help::findOrFail($request->id);

        $help->update([
            'title' => $request->title,
            'body'  => $request->body,
        ]);

        return $help;
    }

    /**
     * Destroys the Help item.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function destroy(Request $request)
    {
        abort_unless($this->mustBeVotenAdministrator(), 403);

        Help::find($request->id)->forceDelete();

        return response('Help item was deleted', 200);
    }
}

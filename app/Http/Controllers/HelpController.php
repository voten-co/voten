<?php

namespace App\Http\Controllers;

use App\Help;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HelpController extends Controller
{
    /**
     * makes sure the user is logged in.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'getHelp', 'show', 'showHelpCenter', 'recentQuestions', 'commonQuestions']]);
    }

    /**
     * index the help models.
     *
     * @return \Illuminate\Support\Collection
     */
    public function index(Request $request)
    {
        $request->validate([
            'filter' => 'string',
        ]);

        return Help::search($request->filter)->take(20)->get();
    }

    /**
     * Return most recent asked questions.
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    public function recentQuestions()
    {
        return Help::orderBy('created_at', 'desc')->take(5)->get();
    }

    /**
     * Return most commonly asked questions (that we specify by the 'index' value).
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    public function commonQuestions()
    {
        return Help::orderBy('index', 'desc')->take(5)->get();
    }

    /**
     * Show the help page.
     *
     * @param Help $help.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Help $help)
    {
        return view('help.show', compact('help'));
    }

    /**
     * Show the help center.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showHelpCenter()
    {
        $recent_questions = $this->recentQuestions();

        $common_questions = $this->commonQuestions();

        return view('help.index', compact('recent_questions', 'common_questions'));
    }

    /**
     * index the help models.
     *
     * @return \Illuminate\Support\Collection
     */
    public function indexAll(Request $request)
    {
        abort_unless($this->mustBeVotenAdministrator(), 403);

        return Help::all();
    }

    /**
     * Returns help record.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function getHelp(Request $request)
    {
        return Help::findOrFail($request->id);
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

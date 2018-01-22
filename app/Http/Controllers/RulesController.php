<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Rule;
use Illuminate\Http\Request;

class RulesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index']]);
    }

    /**
     * indexes all the related records.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        return Channel::where('name', $request->name)->firstOrFail()->rules()->orderBy('created_at', 'desc')->get();
    }

    /**
     * stores a new rule record.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title'         => 'required|max:300',
            'channel_name'  => 'required',
        ]);

        $channel_id = Channel::where('name', $request->channel_name)->value('id');

        abort_unless($this->mustBeAdministrator($channel_id), 403);

        if (Rule::where('channel_id', $channel_id)->count() > 4) {
            return response("can't create more than 5 rules per each channel", 500);
        }

        $rule = new Rule([
            'title'       => $request->title,
            'channel_id'  => $channel_id,
        ]);
        $rule->save();

        return $rule;
    }

    /**
     * patches the model.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function patch(Request $request)
    {
        $this->validate($request, [
            'title'       => 'required',
            'channel_id'  => 'required',
            'rule_id'     => 'required|integer',
        ]);

        abort_unless($this->mustBeAdministrator($request->channel_id), 403);

        Rule::find($request->rule_id)->update([
            'title' => $request->title,
        ]);

        return response('Rule updated successfully', 200);
    }

    /**
     * destroys the rule record.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function destroy(Request $request)
    {
        $this->validate($request, [
            'channel_id'  => 'required|integer',
            'rule_id'     => 'required|integer',
        ]);

        abort_unless($this->mustBeAdministrator($request->channel_id), 403);

        Rule::destroy($request->rule_id);

        return response('Rule was deleted.', 200);
    }
}

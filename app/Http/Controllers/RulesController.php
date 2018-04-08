<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Http\Resources\RuleResource;
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
            'channel_name' => 'required_without:channel_id|exists:channels,name',
            'channel_id'   => 'required_without:channel_name|exists:channels,id',
        ]);

        if (request()->filled('channel_name')) {
            return RuleResource::collection(
                Channel::where('name', $request->channel_name)->first()->rules()->orderBy('created_at', 'desc')->get()
            );
        }

        return RuleResource::collection(
            Channel::where('id', $request->channel_id)->first()->rules()->orderBy('created_at', 'desc')->get()
        );
    }

    /**
     * stores a new rule record.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function store(Request $request, Channel $channel)
    {
        $this->validate($request, [
            'body'       => 'required|string|max:300'
        ]);

        if (Rule::where('channel_id', $channel->id)->count() > 4) {
            return res(400, "can't create more than 5 rules per each channel");
        }

        return new RuleResource(
            Rule::create([
                'title'      => $request->body,
                'channel_id' => $channel->id,
            ])
        );
    }

    /**
     * patches the model.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function patch(Request $request, Channel $channel, Rule $rule)
    {
        $this->validate($request, [
            'body' => 'required',
        ]);

        $rule->update([
            'title' => $request->body,
        ]);

        return res(200, 'Rule updated successfully');
    }

    /**
     * destroys the rule record.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function destroy(Channel $channel, Rule $rule)
    {
        $rule->delete();

        return res(200, 'Rule was deleted.');
    }
}

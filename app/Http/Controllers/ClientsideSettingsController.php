<?php

namespace App\Http\Controllers;

use App\ClientsideSettings;
use Auth;
use Illuminate\Http\Request;

class ClientsideSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store/update the user's clientside settings.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function store(Request $request)
    {
        $request->validate([
            'json'     => 'required|json',
            'platform' => 'in:Web,Desktop,Android,iOS',
        ]);

        $settings = ClientsideSettings::firstOrNew([
            'user_id'  => Auth::id(),
            'platform' => $request->platform,
        ]);

        $settings->json = $request->json;

        $settings->save();

        return res(200, 'Settings saved successfully.');
    }

    /**
     * Store/update the user's clientside settings.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return json
     */
    public function get(Request $request)
    {
        $request->validate([
            'platform' => 'in:Web,Desktop,Android,iOS',
        ]);

        $settings = ClientsideSettings::where([
            ['user_id', Auth::id()],
            ['platform', $request->platform],
        ])->first();

        return optional($settings)->json;
    }
}

<?php

namespace App\Http\Controllers;

use App\Traits\CachableCategory;
use App\Traits\CachableUser;
use Auth;
use Illuminate\Http\Request;

class SubscribeController extends Controller
{
    use CachableUser, CachableCategory;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * subscribing/unsubscrbing to categories.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return status
     */
    public function subscribeToggle(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required|integer',
        ]);

        $user = Auth::user();

        try {
            $result = $user->subscriptions()->toggle($request->category_id);
        } catch (\Exception $e) {
            return response('duplicate action', 200);
        }

        // subscibed
        if ($result['attached']) {
            $this->updateSubscriptions($user->id, $request->category_id, true);

            $this->updateCategorySubscribersCount($request->category_id);

            return response('Subscribed', 200);
        }

        // unsubscribed
        $this->updateSubscriptions($user->id, $request->category_id, false);

        $this->updateCategorySubscribersCount($request->category_id, -1);

        return response('Unsubscribed', 200);
    }

    /**
     * whether or not the user is subscribed to the category.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return string
     */
    public function isSubscribed(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required|integer',
        ]);

        $subscriptions = $this->subscriptions();

        return in_array($request->category_id, $subscriptions) ? 'true' : 'false';
    }
}

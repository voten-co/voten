<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use Illuminate\Http\Request;

class UserSettingsController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * updates user profile info
     *
     * @param  Illuminate\Http\Request $request
     * @return response
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $this->validate($request, [
            'color' => 'in:Dark Blue,Blue,Red,Dark,Pink,Dark Green,Bright Green,Purple,Gray,Orange',
            'location' => 'nullable|string|max:25',
            'name' => 'nullable|string|max:25',
            'bio' => 'nullable|max:400',
            'website' => 'nullable|url',
            'twitter' => 'nullable|string|max:25',
        ]);

        $info = [
            'website' => $request->website,
            'twitter' => $request->twitter
        ];

        $user->update([
        	'color' => $request->color,
            'location' => $request->location,
            'name' => $request->name,
            'bio' => $request->bio,
            'info' => $info
     	]);

        return response("Your settings has been updated", 200);
    }


    /**
     * updates the account settings
     *
     * @param  Illuminate\Http\Request $request
     * @return response
     */
    public function updateAccount(Request $request)
    {
        $user = Auth::user();

        $this->validate($request, [
            'font' => 'in:Josefin Sans,Lato,Source Sans Pro,Ubuntu,Open Sans,Dosis,Reem Kufi,Athiti,Molengo,Catamaran,Roboto,Eczar,Titillium Web,Varela Round,Bree Serif,Alegreya Sans,Sorts Mill Goudy,Patrick Hand,Dancing Script,Satisfy,Gloria Hallelujah,Courgette,Indie Flower,Handlee,Arvo,Montserrat',
            'sidebar_color' => 'in:Dark Blue,Blue,Red,Dark,Purple,Green,Gray',
            'notify_submissions_replied' => 'boolean',
            'notify_comments_replied' => 'boolean',
            'username' => 'min:3|max:25|regex:/^[A-Za-z0-9\._]+$/|unique:users,username,' . $user->id,
        ]);

        $settings = [
            'font' => $request->font,
            'sidebar_color' => $request->sidebar_color,
            'nsfw' => settings('nsfw'),
            'nsfw_media' => settings('nsfw_media'),
            'notify_submissions_replied' => $request->notify_submissions_replied,
            'notify_comments_replied' => $request->notify_comments_replied,
            'exclude_upvoted_submissions' => settings('exclude_upvoted_submissions'),
            'exclude_downvoted_submissions' => settings('exclude_downvoted_submissions'),
            'submission_small_thumbnail' => settings('submission_small_thumbnail')
        ];

        $user->update([
        	'username' => $request->username,
            'settings' => $settings
     	]);

        return response("Your settings has been updated", 200);
    }


    /**
     * udates the settings for the home feed
     *
     * @param  Illuminate\Http\Request $request
     * @return response
     */
    public function updateHomeFeed(Request $request)
    {
        $user = Auth::user();

        $this->validate($request, [
            'submission_small_thumbnail' => 'boolean',
            'nsfw_media' => 'boolean',
            'nsfw' => 'boolean',
            'exclude_downvoted_submissions' => 'boolean',
            'exclude_upvoted_submissions' => 'boolean',
        ]);

        $settings = [
            'font' => settings('font'),
            'sidebar_color' => settings('sidebar_color'),
            'nsfw' => $request->nsfw,
            'nsfw_media' => $request->nsfw_media,
            'notify_submissions_replied' => settings('notify_submissions_replied'),
            'notify_comments_replied' => settings('notify_comments_replied'),
            'exclude_upvoted_submissions' => $request->exclude_upvoted_submissions,
            'exclude_downvoted_submissions' => $request->exclude_downvoted_submissions,
            'submission_small_thumbnail' => isMobileDevice() ? settings('submission_small_thumbnail') : $request->submission_small_thumbnail
        ];

        $user->update([
            'settings' => $settings,
     	]);

        return response("Your settings has been updated", 200);
    }
}

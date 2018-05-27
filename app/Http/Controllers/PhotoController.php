<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Http\Resources\PhotoResource;
use App\Photo;
use App\PhotoTools;
use App\Traits\CachableChannel;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManagerStatic as Image;

class PhotoController extends Controller
{
    use PhotoTools, CachableChannel;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Stores photo's record and file.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return photo_id
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|image|max:10240',
        ]);

        $photo = new Photo();
        $photo->user_id = Auth::id();
        $photo->path = $this->uploadImg($request->file('file'), 'submissions/img');
        $photo->thumbnail_path = $this->createThumbnail($request->file('file'), 600, null, 'submissions/img/thumbs');
        $photo->save();

        return new PhotoResource($photo);
    }

    /**
     * Get the uploaded photo.
     *
     * @param integer $photo
     *
     * @return PhotoResource
     */
    public function get(Photo $photo)
    {
        return new PhotoResource($photo);
    }

    /**
     * upload and update channel's avatar.
     *
     * @return response
     */
    public function channelAvatar(Request $request, Channel $channel)
    {
        // validate
        $this->validate($request, [
            'photo' => ['required', 'image', Rule::dimensions()->minWidth(250)->minHeight(250)->ratio(1 / 1)],
        ]);

        // fill variables
        $filename = time() . str_random(16) . '.png';
        $image = Image::make($request->file('photo')->getRealPath());
        $folder = 'channels/avatars';

        // crop it
        $image = $image->resize(250, 250);

        // optimize it
        $image->encode('png', 60);

        // upload it
        Storage::put($folder . '/' . $filename, $image);
        $imageAddress = $this->webAddress() . $folder . '/' . $filename;

        // delete the old avatar
        Storage::delete('channels/avatars/' . str_after($channel->avatar, 'channels/avatars/'));

        // update channel's avatar
        $channel->update([
            'avatar' => $imageAddress,
        ]);
        $this->putChannelInTheCache($channel);

        return $imageAddress;
    }

    /**
     * upload and update channel's avatar.
     *
     * @return response
     */
    public function userAvatar(Request $request)
    {
        // validate
        $this->validate($request, [
            'photo' => ['required', 'image', Rule::dimensions()->minWidth(250)->minHeight(250)->ratio(1 / 1)],
        ]);

        // fill variables
        $filename = time() . str_random(16) . '.png';
        $image = Image::make($request->file('photo')->getRealPath());
        $folder = 'users/avatars';

        // crop it
        $image = $image->resize(250, 250);

        // optimize it
        $image->encode('png', 60);

        // upload it
        Storage::put($folder . '/' . $filename, $image);
        $imageAddress = $this->webAddress() . $folder . '/' . $filename;

        // delete the old avatar
        if (isset(Auth::user()->avatar)) {
            Storage::delete('users/avatars/' . str_after(Auth::user()->avatar, 'users/avatars/'));
        }

        // update user's avatar
        Auth::user()->update([
            'avatar' => $imageAddress,
        ]);

        return $imageAddress;
    }
}

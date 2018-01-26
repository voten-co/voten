<?php

namespace App\Http\Controllers;

use App\Channel;
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

        // image validation
        if (!$request->hasFile('file') || !$request->file('file')->isValid()) {
            return response('The uploaded photo is not acceptable. Please try another one', 500);
        }

        $photo = new Photo();
        $photo->user_id = Auth::id();

        try {
            $photo->path = $this->uploadImg($request->file('file'), 'submissions/img');
            $photo->thumbnail_path = $this->createThumbnail($photo->path, 1200, null, 'submissions/img/thumbs');
        } catch (\Exception $exception) {
            return response('Ooops, something went wrong', 500);
        }

        $photo->save();

        return $photo->id;
    }

    /**
     * upload and update channel's avatar.
     *
     * @return response
     */
    public function channelAvatar(Request $request)
    {
        // validate
        $this->validate($request, [
            'channel_name' => 'required',
            'photo'        => [
                'required',
                'image',
                Rule::dimensions()->minWidth(250)->minHeight(250)->ratio(1 / 1),
            ],
        ]);
        $channel = Channel::where('name', $request->channel_name)->firstOrFail();
        abort_unless($this->mustBeAdministrator($channel->id), 403);

        // fill variables
        $filename = time().str_random(16).'.png';
        $image = Image::make($request->file('photo')->getRealPath());
        $folder = 'channels/avatars';

        // crop it
        $image = $image->resize(250, 250);

        // optimize it
        if ($image->filesize() > 50000) { // 50kb
            $image->encode('png', 60);
        } else {
            $image->encode('png', 90);
        }

        // upload it
        Storage::put($folder.'/'.$filename, $image);
        $imageAddress = $this->webAddress().$folder.'/'.$filename;

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
            'photo' => [
                'required',
                'image',
                Rule::dimensions()->minWidth(250)->minHeight(250)->ratio(1 / 1),
            ],
        ]);

        // fill variables
        $filename = time().str_random(16).'.png';
        $image = Image::make($request->file('photo')->getRealPath());
        $folder = 'users/avatars';

        // crop it
        $image = $image->resize(250, 250);

        // optimize it
        if ($image->filesize() > 50000) { // 50kb
            $image->encode('png', 60);
        } else {
            $image->encode('png', 90);
        }

        // upload it
        Storage::put($folder.'/'.$filename, $image);
        $imageAddress = $this->webAddress().$folder.'/'.$filename;

        // update user's avatar
        Auth::user()->update([
            'avatar' => $imageAddress,
        ]);

        return $imageAddress;
    }
}

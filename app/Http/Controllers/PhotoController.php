<?php

namespace App\Http\Controllers;

use App\Category;
use App\Photo;
use App\PhotoTools;
use App\Traits\CachableCategory;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class PhotoController extends Controller
{
    use PhotoTools, CachableCategory;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Uploads photo sent by Dropzone.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return photo_id
     */
    public function upload(Request $request)
    {
        $this->validate($request, [
            'photo' => 'required|image|max:10240',
        ]);

        // image validation
        if (!$request->hasFile('photo') || !$request->file('photo')->isValid()) {
            return response('The uploaded photo is not acceptable. Please try another one', 500);
        }

        $photo = new Photo();
        $photo->user_id = Auth::user()->id;

        try {
            $photo->path = $this->uploadImg($request->file('photo'), 'submissions/img');
            $photo->thumbnail_path = $this->createThumbnail($photo->path, 1200, null, 'submissions/img/thumbs');
        } catch (\Exception $exception) {
            return response('Ooops, something went wrong', 500);
        }

        $photo->save();

        return $photo->id;
    }

    /**
     * Uploads the photo temporary and sends it for croping. The uplaoded photo is about to get deleted.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function uploadTempAvatar(Request $request)
    {
        $this->validate($request, [
            'photo' => 'required|image',
        ]);

        return $this->uploadImgPNG($request->file('photo'), 'temp');
    }

    /**
     * Crops the uploaded photo with the sent coordinates and sets it as auth user's avatar.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    public function cropUserAvatar(Request $request)
    {
        $this->validate($request, [
            'photo'  => 'required',
            'width'  => 'required|integer',
            'height' => 'required|integer',
            'x'      => 'required|integer',
            'y'      => 'required|integer',
        ]);

        $user = Auth::user();

        $user->avatar = $this->cropImg(
            $request->photo,
            $request->width,
            $request->height,
            $request->x,
            $request->y,
            'users/avatars'
        );

        $user->update();

        return $user->avatar;
    }

    /**
     * Crops the uploaded photo with the sent coordinates and sets it as category's avatar.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    public function cropCategoryAvatar(Request $request)
    {
        $this->validate($request, [
            'photo'  => 'required',
            'width'  => 'required|integer',
            'height' => 'required|integer',
            'x'      => 'required|integer',
            'y'      => 'required|integer',
        ]);

        $category = Category::where('name', $request->name)->firstOrFail();

        abort_unless($this->mustBeAdministrator($category->id), 403);

        $category->avatar = $this->cropImg(
            $request->photo,
            $request->width,
            $request->height,
            $request->x,
            $request->y,
            'categories/avatars'
        );

        $category->update();

        $this->putCategoryInTheCache($category);

        return $category->avatar;
    }

    protected function createAvatar($image, $folder = 'categories/avatars')
    {
        $filename = time().str_random(7).'.png';
        $image = Image::make($image->getRealPath());

        $image = $image->resize(250, 250);
        $image->encode('png');

        Storage::put($folder.'/'.$filename, $image);

        return $this->webAddress().$folder.'/'.$filename; // must return the exact url(maybe with the cdn url in it)
    }
}

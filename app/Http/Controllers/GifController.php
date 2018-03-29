<?php

namespace App\Http\Controllers;

use App\Gif;
use App\PhotoTools;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Pbmedia\LaravelFFMpeg\FFMpegFacade as FFMpeg;
use App\Http\Resources\GifResource;

class GifController extends Controller
{
    use PhotoTools;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Animated gif submissions: The uploaded .gif file should get converted
     * to .mp4 format to save bandwitch for both server and user. This way
     * we get to use a pretty HTML5 player for playing .gif files.
     *
     * @param Request $request
     *
     * @return int id
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:gif|max:51200',
        ]);

        $file = $request->file('file');

        $filename = time().str_random(16);

        $file->storeAs('submissions/gif', $filename.'.gif', 'local');

        try {
            FFMpeg::fromDisk('local')
                ->open('submissions/gif/'.$filename.'.gif')
                // mp4
                ->export()
                ->toDisk('local')
                ->inFormat((new \FFMpeg\Format\Video\X264())->setAdditionalParameters([
                    '-movflags', 'faststart',
                    '-pix_fmt', 'yuv420p',
                    '-preset', 'veryslow',
                    '-b:v', '500k',
                ]))
                ->save('submissions/gif/'.$filename.'.mp4')
                // thumbnail
                ->getFrameFromSeconds(1)
                ->export()
                ->toDisk('local')
                ->save('submissions/gif/'.$filename.'.jpg');
        } catch (\Exception $exception) {
            return response("We couldn't process the uploaded GIF at this time. Please try another one. ", 500);
        }

        // get the uploaded mp4 and move it to the cloud 
        $mp4 = Storage::disk('local')->get('submissions/gif/'.$filename.'.mp4');
        Storage::disk(config('filesystems.default'))->put('submissions/gif/'.$filename.'.mp4', $mp4);

        // get the uploaded jpg and move it to the cloud 
        $jpg = Storage::disk('local')->get('submissions/gif/'.$filename.'.jpg');
        Storage::disk(config('filesystems.default'))->put('submissions/gif/'.$filename.'.jpg', $jpg);

        // delete temp files from local storage
        Storage::disk('local')->delete([
            'submissions/gif/'.$filename.'.jpg',
            'submissions/gif/'.$filename.'.gif',
            'submissions/gif/'.$filename.'.mp4',
        ]);

        $gif = Gif::create([
            'user_id'        => Auth::id(),
            'mp4_path'       => $this->webAddress().'submissions/gif/'.$filename.'.mp4',
            'thumbnail_path' => $this->webAddress().'submissions/gif/'.$filename.'.jpg',
        ]);

        return new GifResource($gif);
    }
}

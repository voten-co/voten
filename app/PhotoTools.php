<?php

namespace App;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

trait PhotoTools
{
    protected function webAddress()
    {
        return config('filesystems.cdn_url');
    }

    /**
     * Creates and saves a thumbnail for the sent photo and stores into the defined direcory( could be cloud, local...).
     *
     * @param  request('img')
     * @param  (int) width of the thumbnail
     * @param  (int) height of the thumbnail
     * @param  (string) directory the file should be uploaded to
     *
     * @return (string) the path of uploaded file
     */
    protected function createThumbnail($url, $width, $height, $folder = 'submissions/img/thumbs')
    {
        $filename = time().str_random(16).'.jpg';
        $image = Image::make($url);

        if ($image->width() > 600) {
            if ($width == null || $height == null) {
                $image = $image->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                });
            } else {
                $image = $image->resize($width, $height);
            }
        }

        $image->encode(null, 60);
        Storage::put($folder.'/'.$filename, $image);

        return $this->webAddress().$folder.'/'.$filename;
    }

    /**
     * Crops the uploaded photo with the sent coordinates.
     *
     * @param string $url
     * @param int    $width
     * @param int    $height
     * @param int    $x
     * @param int    $y
     * @param string $folder
     *
     * @return string
     */
    protected function cropImg($url, $width, $height, $x, $y, $folder = 'users/avatars')
    {
        $filename = time().str_random(16).'.png';
        $image = Image::make($url);
        $image = $image->crop($width, $height, $x, $y);

        // optimize the croped image (decrease filesize)
        $image = $image->resize(250, 250);

        $image->encode('png', 60);

        Storage::put($folder.'/'.$filename, $image);

        return $this->webAddress().$folder.'/'.$filename;
    }

    /**
     * Uplaods the file into the defined direcory( could be cloud, local...). Encodes all the formates
     * into jpg, also optimizes it so the uploaded photos will have less size. In case the image is
     * in .gif format, it doesn't touch it. Just uploads the file while keeping the .gif format.
     *
     * @param  request('img')
     * @param  (string) directory the file should be uploaded to
     *
     * @return (string) the path of uploaded file
     */
    protected function uploadImg($image, $folder = 'submissions/img')
    {
        $filename = time().str_random(16).'.jpg';
        $image = Image::make($image->getRealPath());

        if (!$image->filesize()) {
            return;
        }

        $image->encode('jpg', 60);

        Storage::put($folder.'/'.$filename, $image);

        return $this->webAddress().$folder.'/'.$filename;
    }

    /**
     * Uplaods the file into the defined direcory( could be cloud, local...).
     *
     * @param  request('img')
     * @param  (string) directory the file should be uploaded to
     *
     * @return (string) the path of uploaded file
     */
    protected function uploadImgPNG($image, $folder = 'submissions/img')
    {
        $filename = time().str_random(16).'.png';
        $image = Image::make($image->getRealPath());

        $image->encode('png');

        Storage::put($folder.'/'.$filename, $image);

        return $this->webAddress().$folder.'/'.$filename;
    }

    /**
     * Downloads the image from the external link and stores into the defined direcory( could be cloud, local...).
     *
     * @param  (string) external url
     * @param  (string) directory the file should be uploaded to
     *
     * @return (string) the path of uploaded file
     */
    protected function downloadImg($url, $folder = 'submissions/link')
    {
        $filename = time().str_random(16).'.jpg';
        $image = Image::make($url);

        $image->encode('jpg', 60);

        Storage::put($folder.'/'.$filename, $image);

        return $this->webAddress().$folder.'/'.$filename;
    }
}

<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class DocumentController extends Controller
{

    /**
     * @param $file
     * @param Request $request
     * @return \Illuminate\Http\Response|mixed
     */
    public function get_images_url($file,Request $request)
    {
        $w = $request->get('w','');
        $h = $request->get('h','');
        $file = base64_decode($file);
        $path = Storage::disk('localupload')->url($file);
        $image = Image::cache(function($image) use ($path,$w,$h) {
            $info = getimagesize($path);
            $width = empty($w) ? $info[0] : $w;
            $height = empty($h) ? $info[1] : $h;
            return $image->make($path)->resize($width, $height);
        }, 10, true);

        return $image->response($image->mime());
    }

    /**
     * @param $file
     * @return mixed
     */
    public function get_file_url($file)
    {
        return response()->file(Storage::disk('localupload')->url(base64_decode($file)));
    }
}
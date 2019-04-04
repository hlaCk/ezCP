<?php

namespace hlaCk\ezCP\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Constraint;
use Intervention\Image\Facades\Image;
use hlaCk\ezCP\Facades\ezCP;

class ezCPController extends Controller
{
    public function index()
    {
//        Lang::setLocale("en");
//        d(
//            Lang::getLocale(),
//            __('ezcp::seeders.menu_items.tools')
//            __('ezcp::generic.dashboard')
//        );
        return ezCP::view('ezcp::index');
    }

    public function logout()
    {
        app('ezCPAuth')->logout();

        return redirect()->route('ezcp.login');
    }

    public function upload(Request $request)
    {
        $fullFilename = null;
        $resizeWidth = 1800;
        $resizeHeight = null;
        $slug = $request->input('type_slug');
        $file = $request->file('image');

        $path = $slug.'/'.date('F').date('Y').'/';

        $filename = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension());
        $filename_counter = 1;

        // Make sure the filename does not exist, if it does make sure to add a number to the end 1, 2, 3, etc...
        while (Storage::disk(config('ezcp.storage.disk'))->exists($path.$filename.'.'.$file->getClientOriginalExtension())) {
            $filename = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension()).(string) ($filename_counter++);
        }

        $fullPath = $path.$filename.'.'.$file->getClientOriginalExtension();

        $ext = $file->guessClientExtension();

        if (in_array($ext, ['jpeg', 'jpg', 'png', 'gif'])) {
            $image = Image::make($file)
                ->resize($resizeWidth, $resizeHeight, function (Constraint $constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            if ($ext !== 'gif') {
                $image->orientate();
            }
            $image->encode($file->getClientOriginalExtension(), 75);

            // move uploaded file from temp to uploads directory
            if (Storage::disk(config('ezcp.storage.disk'))->put($fullPath, (string) $image, 'public')) {
                $status = __('ezcp::media.success_uploading');
                $fullFilename = $fullPath;
            } else {
                $status = __('ezcp::media.error_uploading');
            }
        } else {
            $status = __('ezcp::media.uploading_wrong_type');
        }

        // echo out script that TinyMCE can handle and update the image in the editor
        return "<script> parent.helpers.setImageValue('".ezCP::image($fullFilename)."'); </script>";
    }

    public function assets(Request $request)
    {
        $path = str_start(str_replace(['../', './'], '', urldecode($request->path)), '/');
        $path = base_path('packages/hlack/ezcp/publishable/assets'.$path);

        if (File::exists($path)) {
            $mime = '';
            if (ends_with($path, '.js')) {
                $mime = 'text/javascript';
            } elseif (ends_with($path, '.css')) {
                $mime = 'text/css';
            } else {
                $mime = File::mimeType($path);
            }
            $response = response(File::get($path), 200, ['Content-Type' => $mime]);
            $response->setSharedMaxAge(31536000);
            $response->setMaxAge(31536000);
            $response->setExpires(new \DateTime('+1 year'));

            return $response;
        }

        return response('', 404);
    }
}

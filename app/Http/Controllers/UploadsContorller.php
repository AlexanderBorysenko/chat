<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadsContorller extends Controller
{
    // load media file from storage/app/uploads
    public function show($filename)
    {
        $path = storage_path('app/uploads/' . $filename);
        if (!file_exists($path)) {
            abort(404);
        }
        $file = file_get_contents($path);
        $type = mime_content_type($path);
        return response($file, 200)->header('Content-Type', $type);
    }
}

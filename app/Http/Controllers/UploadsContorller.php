<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;

use Illuminate\Http\Request;

class UploadsContorller extends Controller
{
    // load media file from storage/app/uploads
    public function show(Request $request)
    {
        $filename = $request->filename;
        $path = storage_path('app/uploads/' . $filename);
        if (!file_exists($path)) {
            abort(404);
        }

        // Розшифрувати файл перед відображенням
        $encryptedFile = file_get_contents($path);
        $file = Crypt::decryptString($encryptedFile);

        $type = mime_content_type($path);
        return response($file, 200)->header('Content-Type', $type);
    }
}

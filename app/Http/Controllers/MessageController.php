<?php

namespace App\Http\Controllers;

use App\Events\NewDirectMessage;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Intervention\Image\Facades\Image;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Chat $chat)
    {
        $message = $request->validate([
            'content' => 'required|string',
        ]);

        // wrap content links in anchor tags (get link title from meta tags)
        function get_title($url)
        {
            $str = file_get_contents($url);
            $str = mb_convert_encoding($str, 'UTF-8', mb_detect_encoding($str, 'UTF-8, ISO-8859-1', true));

        }
        $message['content'] = preg_replace_callback(
            '/(https?:\/\/[^\s]+)/m',
            function ($matches) {
                $url = $matches[0];
                $html = file_get_contents($url);
                $html = mb_convert_encoding($html, 'UTF-8', mb_detect_encoding($html, 'UTF-8, ISO-8859-1', true));

                preg_match("/\<title\>(.*)\<\/title\>/", $html, $foundedTitle);
                $title = count($foundedTitle) ? $foundedTitle[1] : 'Посиланнячко';

                return '<a href="' . $url . '" target="_blank">' . $title . '</a>';
            },
            $message['content']
        );

        $message['content'] = nl2br($message['content']);
        $message['type'] = 'text';
        $message['sender_id'] = auth()->id();
        $message['chat_id'] = $chat->id;

        $storedMessage = Message::create($message);

        broadcast(new NewDirectMessage($storedMessage))->toOthers();
        return back();
    }

    public function storeMediaFile(Request $request, Chat $chat)
    {
        $request->validate([
            'file' => 'required|file|max:100000',
        ]);
        $file = $request->file('file');

        $message = [
            'content' => 'Формат файла не підтримується',
            'sender_id' => auth()->id(),
            'chat_id' => $chat->id,
        ];

        $fileName = substr(md5(microtime()), rand(0, 26), 5) . "." . $file->getClientOriginalExtension();

        //if file is image condition
        if (in_array($file->getClientOriginalExtension(), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $img = Image::make($file);
            $img->resize(1000, null, function ($constraint) {
                $constraint->aspectRatio();
            })->orientate();

            // ensure file name is unique in the storage folder
            $fileName = substr(md5(microtime()), rand(0, 26), 5) . "." . $file->getClientOriginalExtension();

            // Encrypt and save the image
            $encryptedImg = Crypt::encryptString($img->encode());
            Storage::put('uploads/' . $fileName, $encryptedImg);

            $message['type'] = 'image';
            $message['content'] = $fileName;
        }

        if (in_array($file->getClientOriginalExtension(), ['mp4', 'mov'])) {
            // if mov convert it to mp4
            if ($file->getClientOriginalExtension() == 'mov') {
                // Define the source and destination video paths
                $file->storeAs('uploads/' . $fileName);
                $srcFile = storage_path('app/uploads/' . $fileName);

                // Open the video file
                $ffmpeg = \FFMpeg\FFMpeg::create([
                    'ffmpeg.binaries' => '/usr/bin/ffmpeg',
                    'ffprobe.binaries' => '/usr/bin/ffprobe'
                ]);
                $video = $ffmpeg->open($srcFile);
                $format = new \FFMpeg\Format\Video\X264();
                $format->setAudioCodec("libmp3lame")->setKiloBitrate(0);
                $video->save($format, storage_path('app/uploads/' . pathinfo($fileName, PATHINFO_FILENAME) . '.mp4'));

                // Update the file name to the new mp4 file
                $fileName = pathinfo($fileName, PATHINFO_FILENAME) . '.mp4';
                unlink($srcFile);
            } else {
                // Encrypt and save the video file
                $encryptedFile = Crypt::encryptString(file_get_contents($file));
                Storage::put('uploads/' . $fileName, $encryptedFile);
            }

            $message['type'] = 'video';
            $message['content'] = $fileName;
        }

        $storedMessage = Message::create($message);
        broadcast(new NewDirectMessage($storedMessage))->toOthers();
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

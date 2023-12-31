<?php

namespace App\Http\Controllers;

use App\Events\NewDirectMessage;
use App\Events\ReadUserMessages;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Crypt;
use Intervention\Image\Facades\Image;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class DirectMessagesController extends Controller
{
    public function index()
    {
        $users = User::where('id', '!=', auth()->id())->withCount('currentUserUnreadMessages')->get();
        return Inertia::render('DirectMessages/Index', [
            'users' => $users,
        ]);
    }

    public function show(User $user)
    {
        $messages = $user->currentUserChat;
        // ->with('sender')->between(auth()->id(), $user->id)->get();
        return Inertia::render('DirectMessages/Show', [
            'messages' => $messages,
            'user' => $user,
        ]);
    }

    public function store(Request $request, User $user)
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

        $message['sender_id'] = auth()->id();
        $message['receiver_id'] = $user->id;

        $storedMessage = Message::create($message);

        broadcast(new NewDirectMessage($storedMessage))->toOthers();
        return back();
    }

    // accepts file
    // compress the image too 1000px width, auto height and store it in the public folder.
    // add <img src="path/to/image" alt="image" loading="lazy"/> to the message content
    public function storeMediaFileMessage(Request $request, User $user)
    {
        // get input file mime type
        $request->validate([
            'file' => 'required|file|max:100000',
        ]);
        $file = $request->file('file');

        $message = [
            'content' => 'Формат файла не підтримується',
            'sender_id' => auth()->id(),
            'receiver_id' => $user->id,
            'type' => 'media',
        ];

        if (file_exists(storage_path('app/uploads/' . $file->getClientOriginalName()))) {
            $fileName = $file->getClientOriginalName() . '-' . time() . '.' . $file->getClientOriginalExtension();
        } else {
            $fileName = $file->getClientOriginalName();
        }

        //if file is image condition
        if (in_array($file->getClientOriginalExtension(), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $img = Image::make($file);
            $img->resize(1000, null, function ($constraint) {
                $constraint->aspectRatio();
            })->orientate();

            // ensure file name is unique in the storage folder
            if (file_exists(storage_path('app/uploads/' . $file->getClientOriginalName()))) {
                $fileName = $file->getClientOriginalName() . '-' . time() . '.' . $file->getClientOriginalExtension();
            } else {
                $fileName = $file->getClientOriginalName();
            }

            // Encrypt and save the image
            $encryptedImg = Crypt::encryptString($img->encode());
            Storage::put('uploads/' . $fileName, $encryptedImg);

            $message['content'] = '<img src="/uploads?filename=' . $fileName . '" alt="image" loading="lazy"/>';
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

            $message['content'] = '<video controls="controls" muted><source src="/uploads?filename=' . $fileName . '" type="video/mp4"></video>';
        }

        $storedMessage = Message::create($message);
        broadcast(new NewDirectMessage($storedMessage))->toOthers();
        return back();
    }


    public function ajaxReadMessages(Request $request, Chat $chat)
    {
        Message::where('chat_id', $chat->id)->where('sender_id', '!=', auth()->id())->update(['read' => true]);

        broadcast(new ReadUserMessages($chat))->toOthers();
        return response()->json(['success' => true]);
    }

    public function erase(Request $request)
    {
        Message::truncate();

        // delete all files in the uploads folder
        $files = glob(storage_path('app/uploads/*'));

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // regenerate APP_KEY in .env file and clear cache in code below
        $newKey = 'base64:' . base64_encode(
            random_bytes(
                $request->has('keysize') ? $request->input('keysize') : 32
            )
        );

        file_put_contents(base_path('.env'), preg_replace(
            '/^APP_KEY=.*$/m',
            'APP_KEY=' . $newKey,
            file_get_contents(base_path('.env'))
        )
        );

        Artisan::call('config:clear');

        return redirect()->route('exit');
    }
}

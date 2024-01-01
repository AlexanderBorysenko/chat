<?php

namespace App\Http\Controllers;

use App\Events\ReadUserMessages;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Chat/Index', [
            'chats' => auth()->user()->chats()->get(),
        ]);
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Chat $chat)
    {
        return Inertia::render('Chat/Show', [
            'chat' => $chat->load('messages'),
        ]);
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

    public function ajaxReadMessages(Request $request, Chat $chat)
    {
        Message::where('chat_id', $chat->id)->where('sender_id', '!=', auth()->id())->update(['read' => true]);

        broadcast(new ReadUserMessages($chat))->toOthers();
        return response()->json(['success' => true]);
    }

    public function erase(Request $request, Chat $chat)
    {
        // go throught all messages in Chat and delete files with filenames from content
        foreach ($chat->messages as $message) {
            if ($message->type == 'image' || $message->type == 'video') {
                $fileName = $message->content;
                $file = storage_path('app/uploads/' . $fileName);
                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }

        $chat->messages()->delete();

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('exit');

    }
}

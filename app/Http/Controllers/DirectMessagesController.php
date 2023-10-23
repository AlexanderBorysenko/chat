<?php

namespace App\Http\Controllers;

use App\Events\NewDirectMessage;
use App\Events\ReadUserMessages;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

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

        $message['sender_id'] = auth()->id();
        $message['receiver_id'] = $user->id;

        $storedMessage = Message::create($message);

        broadcast(new NewDirectMessage($storedMessage))->toOthers();
        return back();
    }

    public function ajaxReadMessages(Request $request, User $user)
    {
        Message::where('sender_id', $user->id)->where('receiver_id', auth()->id())->update(['read' => true]);

        broadcast(new ReadUserMessages($user))->toOthers();
        return response()->json(['success' => true]);
    }

    public function erase(Request $request)
    {
        Message::truncate();

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('exit');
    }
}

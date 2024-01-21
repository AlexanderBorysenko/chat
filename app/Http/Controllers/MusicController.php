<?php

namespace App\Http\Controllers;

use App\Http\Requests\MusicStoreRequest;
use App\Models\Music;
use Illuminate\Http\Request;

class MusicController extends Controller
{
    public function index()
    {
        return inertia('Music/Index')->with([
            //order by id desc
            'music' => Music::all()->sortByDesc('id'),
        ]);
    }

    public function create()
    {
        return inertia('Music/Create');
    }

    public function store(MusicStoreRequest $request)
    {
        $request->validated();

        $path = $request->file('file')->store('public/music');
        $url = str_replace('public/', '/storage/', $path);

        Music::create([
            'name' => $request->name,
            'description' => $request->description,
            'path' => $path,
            'url' => $url,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('music.create')->with('success', 'Music created successfully');
    }

    public function destroy(Music $music)
    {
        $music->delete();

        return redirect()->route('music.index')->with('success', 'Music deleted successfully');
    }
}

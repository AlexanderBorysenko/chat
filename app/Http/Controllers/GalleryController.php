<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index()
    {
        $allImagesInGallery = collect(\Storage::files('public/gallery'))->map(function ($file) {
            return str_replace('public/', '/storage/', $file);
        });

        return inertia('Gallery/Index')->with([
            'images' => $allImagesInGallery,
        ]);
    }

    public function create()
    {
        return inertia('Gallery/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|image',
        ]);

        $request->file('file')->store('public/gallery');

        return redirect()->route('gallery.create')->with('success', 'Image created successfully');
    }
}

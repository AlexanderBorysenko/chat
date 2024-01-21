<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MusicController;
use App\Http\Controllers\UploadsContorller;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('auth')->group(function () {
    Route::get('/', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{chat}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/ajaxReadMessages/{chat}', [ChatController::class, 'ajaxReadMessages'])->name('chat.ajaxReadMessages');

    Route::post('/message/store/{chat}', [MessageController::class, 'store'])->name('message.store');
    Route::post('/message/storeMediaFile/{chat}', [MessageController::class, 'storeMediaFile'])->name('message.storeMediaFile');

    Route::get('/uploads', [UploadsContorller::class, 'show'])->name('upload.show');

    // Music index, creaete and store routes
    Route::get('/music', [MusicController::class, 'index'])->name('music.index');
    Route::get('/music/create', [MusicController::class, 'create'])->name('music.create');
    Route::post('/music/store', [MusicController::class, 'store'])->name('music.store');
    Route::delete('/music/{music}', [MusicController::class, 'destroy'])->name('music.delete');

    // ajaxReadMessages

    Route::post('/erase/{chat}', [ChatController::class, 'erase'])->name('chat.erase');

    // uploads
});

Route::get('/exit', function () {
    return Inertia::render('Exit');
})->name('exit');

require __DIR__ . '/auth.php';

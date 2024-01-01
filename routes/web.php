<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\DirectMessagesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MessageController;
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

    // ajaxReadMessages

    Route::post('/erase/{chat}', [ChatController::class, 'erase'])->name('chat.erase');

    // uploads
});

Route::get('/exit', function () {
    return Inertia::render('Exit');
})->name('exit');

require __DIR__ . '/auth.php';

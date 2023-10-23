<?php

use App\Http\Controllers\DirectMessagesController;
use App\Http\Controllers\HomeController;
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
    // Route::get('/', HomeController::class)->name('home');

    // Route::get('/post/create', HomeController::class)->name('home');

    // Route::get('/directs', [DirectMessagesController::class, 'index'])->name('directMessages.index');
    Route::get('/', [DirectMessagesController::class, 'index'])->name('directMessages.index');
    Route::get('/direct/{user}', [DirectMessagesController::class, 'show'])->name('directMessages.show');
    Route::post('/direct/store/{user}', [DirectMessagesController::class, 'store'])->name('directMessages.store');

    Route::post('/ajaxReadMessages/{user}', [DirectMessagesController::class, 'ajaxReadMessages'])->name('directMessages.ajaxReadMessages');
    // ajaxReadMessages

    Route::post('/erase', [DirectMessagesController::class, 'erase'])->name('directMessages.erase');
});

require __DIR__ . '/auth.php';

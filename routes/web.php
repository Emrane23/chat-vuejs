<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/chat', [App\Http\Controllers\ChatsController::class, 'index']);
Route::get('/chatprivate', [App\Http\Controllers\ChatsController::class, 'PrivateChat']);
Route::get('/messages', [App\Http\Controllers\ChatsController::class, 'fetchMessages']);
Route::get('/privatemessages/{receiverid}', [App\Http\Controllers\ChatsController::class, 'fetchPrivateMessages']);
Route::get('/getreceiver/{receiverid}', [App\Http\Controllers\ChatsController::class, 'getReceiver']);
Route::get('/showmore/{paginate}', [App\Http\Controllers\ChatsController::class, 'showMore']);
Route::get('/showmoreprivate/{paginate}/{receiverid}', [App\Http\Controllers\ChatsController::class, 'showMorePrivate']);
Route::post('/messages', [App\Http\Controllers\ChatsController::class, 'sendMessage']);
Route::get('/users', [App\Http\Controllers\ChatsController::class, 'getUsers']);
Route::post('/privatemessages/{receieverid}', [App\Http\Controllers\ChatsController::class, 'sendPrivateMessage']);


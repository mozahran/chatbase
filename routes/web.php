<?php

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

use App\User;

Route::get('/', function (\App\Repositories\Interfaces\ChatRepositoryInterface $chat) {
    return view('welcome');
});

Route::get('/chat', function (\App\Repositories\Interfaces\ChatRepositoryInterface $chat) {

    $creator = factory(\App\User::class)->create();
    $recipient = factory(\App\User::class)->create();
    $conversation = $chat->createConversation($creator, [$creator, $recipient]);
    $chat->createReply($conversation, $creator, 'hello world');

    $users = $chat->getChatUsers($conversation);

    $conversation->delete();
    $creator->delete();
    $recipient->delete();

return  $users;
});

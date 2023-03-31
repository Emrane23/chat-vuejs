<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('chat');
    }

    public function fetchMessages()
    {
        $messages = array();
        $records = Message::with('user')->get()->sortByDesc('created_at')->take(5);
        foreach ($records as $key => $value) {
            array_unshift($messages,$value);
        }
        return $messages;
    }

    public function showMore($paginate)
    {
        $messages = array();
        $records = Message::with('user')->get()->sortByDesc('created_at')->take($paginate);
        foreach ($records as $key => $value) {
            array_unshift($messages,$value);
        }
        return $messages;
    }

    public function sendMessage(Request $request)
    {
        $user = Auth::user();
        $message = $user->messages()->create([
            'message' => $request->input('message')
        ]);
        broadcast(new MessageSent($user, $message))->toOthers();
        return ['status' => 'Message Sent!'];
    }
}


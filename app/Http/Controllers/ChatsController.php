<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Events\PrivateMessageSent;
use App\Models\Message;
use App\Models\PrivateMessage;
use App\Models\User;
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

    public function fetchPrivateMessages($receiverid)
    {

        $messages = array();
        $records = PrivateMessage::where(function ($query) use ($receiverid) {
            $query->where('sender_id',Auth::user()->id)
                  ->orWhere('sender_id', $receiverid);
        }
            )->where(function ($query) use ($receiverid){
                $query->where('receiver_id',$receiverid)
                      ->orWhere('receiver_id',Auth::user()->id);
            })->with(["sender","receiver"])->get()->sortByDesc('created_at')->take(5);
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

    public function sendPrivateMessage(Request $request, $receiverid)
    {
        $user = Auth::user();
        $message = PrivateMessage:: create([
            'message' => $request->message,
            'receiver_id' => $receiverid,
            'sender_id' => $user->id
        ]);
         broadcast(new PrivateMessageSent($user, $message))->toOthers();
        return ['status' => 'Message Sent!'];

    }

    public function PrivateChat()
    {
        return view('chat-private');
    }

    public function getReceiver($receiverid)
    {
        $receiver = User::where('id',$receiverid)->first();
        return $receiver ;
    }

    public function getUsers()
    {
        $users = User::where('id','<>',Auth::user()->id)->get();
        return $users;
    }
}


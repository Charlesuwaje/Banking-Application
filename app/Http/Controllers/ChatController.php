<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use NewMessage;
use Pusher\Pusher;

class ChatController extends Controller
{


    public function index()
    {
        $users = User::where('id', '!=', auth()->id())->get();
        $messages = Message::where('user_id', auth()->id())
            ->orWhere('receiver_id', auth()->id())
            ->orderBy('created_at', 'asc')
            ->get();

        return view('chat.index', compact('users', 'messages'));
    }


    public function send(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string',
            'file' => 'nullable|file|max:2048',
            'receiver_id' => 'required|exists:users,id',
        ]);

        $data = [
            'user_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
        ];

        if ($request->message) {
            $data['message'] = $request->message;
        }

        if ($request->hasFile('file')) {
            $data['file'] = $request->file('file')->store('chat_files', 'public');
        }

        $message = Message::create($data);

        return response()->json(['message' => $message], 200);
        // return redirect()->route('chat.index')->with('success', 'Transfer successful.');

    }





    public function fetchMessages()
    {
        $messages = Message::with('user')->latest()->take(50)->get()->reverse();
        return response()->json($messages);
    }

    public function getUsers()
    {
        $users = User::where('id', '!=', auth()->id())
            ->select('id', 'name', 'email')
            ->get();

        return response()->json(['users' => $users], 200);
    }
}

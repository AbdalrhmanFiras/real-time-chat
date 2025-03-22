<?php

namespace App\Http\Controllers;

use App\Events\MessageSend;
use App\Models\Message;
use Illuminate\Container\Attributes\Storage;
use Illuminate\Http\Request;
class MessageController extends Controller
{
    public function Sendmessage(Request $request)
    {

        $request->validate([
            'receiver_id' => 'required|string|user,id',
            'message' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
        ]);

        $filepath = null;
        if ($request->hasFile('file')) {
            $filePath = Storage::disk('public')->put('chat_files', $request->file('file'));
        }


        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'file_path' => $filepath
        ]);

        broadcast(new MessageSend($message))->toOthers();


        return response()->json($message, 201);

    }
}

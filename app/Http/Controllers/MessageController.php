<?php

namespace App\Http\Controllers;

use App\Events\MessageSend;
use App\Models\Message;
use Illuminate\Container\Attributes\Storage;
use Illuminate\Http\Request;
class MessageController extends Controller
{
    public function Sendmessage(Request $request)
    {// post

        $request->validate([
            'receiver_id' => 'required|string|user,id',
            'message' => 'nullable|string',
            'file' => 'nullable|file|max:10240',// 10mb max
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
        // send message that i made to everyone is online(event)

        return response()->json($message, 201);

    }

    public function Getmessage($receiver_id)
    {

        $messages = Message::where(function ($query) use ($receiver_id) {
            $query->where('sender_id', auth()->id())
                ->where('receiver_id', $receiver_id);
        })->orWhere(function ($query) use ($receiver_id) {
            $query->where('sender_id', $receiver_id)
                ->where('receiver_id', auth()->id());
        })->orderBy('create_at', 'asc')->paginate(15);
        // الرسائل في قاعدة البيانات
        // id	sender_id	receiver_id	message	created_at
        // 1	1	2	مرحبًا!	2023-10-01 12:00:00
        // 2	2	1	أهلاً!	2023-10-01 12:05:00
        // 3	1	2	كيف حالك؟	2023-10-01 12:10:00
        // 4	2	1	بخير، وأنت؟	2023-10-01 12:15:00
        return response()->json($messages);
    }


}

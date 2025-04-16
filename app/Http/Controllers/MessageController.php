<?php

namespace App\Http\Controllers;

use App\Events\MessageDelete;
use App\Events\MessageSend;
use App\Events\MessageRead;
use App\Events\MessageTyping;
use App\Events\MessageUpdate;
use App\Http\Requests\CreateMessage;
use App\Models\Message;
use App\Http\Resources\MessageResource;
use Illuminate\Container\Attributes\Storage;
use Illuminate\Http\Request;
class MessageController extends Controller
{
    public function Sendmessage(CreateMessage $request)
    {// post


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

        return response()->json(new MessageResource($message), 201);

    }

    public function Getmessage($receiver_id)
    {
        $messages = Message::where(function ($query) use ($receiver_id) {
            $query->where('sender_id', auth()->id())
                ->where('receiver_id', $receiver_id);
        })->orWhere(function ($query) use ($receiver_id) {
            $query->where('sender_id', $receiver_id)
                ->where('receiver_id', auth()->id());
        })->orderBy('created_at', 'asc')->paginate(15);
        // الرسائل في قاعدة البيانات
        // id	sender_id	receiver_id	message	created_at
        // 1	1	2	مرحبًا!	2023-10-01 12:00:00
        // 2	2	1	أهلاً!	2023-10-01 12:05:00
        // 3	1	2	كيف حالك؟	2023-10-01 12:10:00
        // 4	2	1	بخير، وأنت؟	2023-10-01 12:15:00
        return response()->json(MessageResource::collection($messages));
    }

    public function MarkAsRead($message_id)
    {
        $message = Message::where('id', $message_id)->first();

        if (!$message) {
            return response()->json(['error' => 'Message not found'], 404);
        }
        if (!$message->read_at) { // Update only if it's unread
            $message->update(['read_at' => now()]);

            // Broadcast the message read event to the sender
            broadcast(new MessageRead($message))->toOthers();
        }
        return response()->json(new MessageResource($message));

    }


    public function TypingMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
        ]);

        broadcast(new MessageTyping(auth()->user()?->getModel(), $request->receiver_id))->toOthers();
        return response()->json(['status' => 'Typing started']);
    }


    public function UpdateMessage(Request $request, $message_id)
    {

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = Message::find($message_id);
        if (!$message) {
            return response()->json(['error' => 'Message not found'], 404);
        }

        if ($message->sender_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->update(['message' => $request->message]);

        broadcast(new MessageUpdate($message))->toOthers();

        return response()->json([
            'message' => 'Message updated successfully',
            new MessageResource($message)
        ]);
    }


    public function DeltetMessage($message_id)
    {

        $message = Message::find($message_id);

        if (!$message)
            return response()->json(['error' => 'Message not found'], 404);

        if ($message->sender_id !== auth()->id())
            return response()->json(['error' => 'Unauthorized'], 403);



        $message->delete();

        broadcast(new MessageDelete($message))->toOthers();

        return response()->json('the message deleted');


    }
}





<?php

namespace App\Http\Controllers;
use App\Events\MyEvent;
use Illuminate\Http\Request;
use log;
class MyController extends Controller
{
    public function sendMessage(Request $request)
    {
        $message = $request->input('message');

        Log::info('Message sent:', ['message' => $message]);


        // Dispatch the event
        broadcast(new MyEvent($message));

        return response()->json(['status' => 'Message sent!']);
    }
}

<?php

namespace App\Http\Controllers;

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


    }
}

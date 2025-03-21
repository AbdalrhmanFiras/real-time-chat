<?php

namespace App\Http\Controllers;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;
class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:225',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password())
        ]);


    }
}

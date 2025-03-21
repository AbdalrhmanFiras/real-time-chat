<?php

namespace App\Http\Controllers;
use App\Models\User;
use Hash;
use App\Http\Requests\RegisterForm;
use Illuminate\Http\Request;
class AuthController extends Controller
{
    public function register(RegisterForm $request)
    {


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);


        return response()->json([
            'user' => $user,
            'message' => 'user registerd successfully'
        ], 200);

    }
}

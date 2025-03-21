<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Http\Requests\RegisterForm;
use Illuminate\Http\Request;
use Hash;
use Auth;
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


    public function login(Request $request)
    {

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'invalid email or password'], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();//get the user by email

        return response()->json([
            'user' => $user,
            'token' => $token = $user->createToken('auth-token')->plainTextToken
        ], 200);

    }
}

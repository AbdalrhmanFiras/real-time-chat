<?php

namespace App\Http\Controllers;
use App\Http\Resources\AuthResource;
use App\Http\Resources\StatusUserResource;
use App\Models\User;
use App\Http\Requests\RegisterForm;
use App\Models\UserStatus;
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

        $userStatus = UserStatus::create([
            'user_id' => $user->id,
            'is_online' => true
        ]);

        return response()->json([
            'user' => new AuthResource($user),
            'status' => new StatusUserResource($userStatus),
            'message' => 'user registerd successfully'
        ], 200);



    }


    public function login(Request $request)
    {

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'invalid email or password'], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();//get the user by email

        $userStatus = UserStatus::updateOrCreate(
            ['user_id' => $user->id],
            ['is_online' => true, 'last_seen_at' => null]
        );

        return response()->json([
            'user' => new AuthResource($user),
            'status' => new StatusUserResource($userStatus),
            'token' => $token = $user->createToken('auth-token')->plainTextToken
        ], 200);


    }

    public function logout(Request $request)
    {
        // Update user status to offline
        $userStatus = UserStatus::where('user_id', $request->user()->id)->update([
            'is_online' => false,
            'last_seen_at' => now(),
        ]);

        // Revoke the user's token
        $request->user()->tokens()->delete();

        // Return response with updated status
        return response()->json([
            'message' => 'Logged out successfully',
            'status' => new StatusUserResource($userStatus),
        ]);
    }
}

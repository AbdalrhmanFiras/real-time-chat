<?php

namespace App\Http\Controllers;
use App\Http\Resources\AuthResource;
use App\Http\Resources\StatusUserResource;
use App\Models\Message;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Http\Requests\RegisterForm;
use App\Models\UserStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Hash;
use Auth;
use Twilio\Rest\Client;
class AuthController extends Controller
{
    public function register(RegisterForm $request)
    {

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password)
        ]);

        $otp = mt_rand(100000, 999999);
        $user->otp = $otp;
        $user->expired_at = Carbon::now()->addMinutes(5);
        $user->save();

        $userStatus = UserStatus::create([
            'user_id' => $user->id,
            'is_online' => true
        ]);

        $message = "your otp code is " . $otp;
        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_AUTH_TOKEN");
        $number = getenv("TWILIO_FROM");
        $client = new Client($account_sid, $auth_token);
        $client->Messages->create('+9647722881560', [
            'from' => $number,
            'body' => $message
        ]);

        // Mail::send('emails.otp', ['otp' => $otp], function ($message) use ($user) {
        //     $message->to($user->email)
        //         ->subject('Your OTP for Email Verification');
        // });

        return response()->json([
            'otp' => 'your Otp Sent to email',
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
        $userStatus->refresh();

        $user->save();
        // Mail::send('emails.otp', ['otp' => $otp], function ($message) use ($user) {
        //     $message->to($user->email)
        //         ->subject('Your OTP for Email Verification');
        // });



        Mail::send('emails.otp', ['user' => $user], function ($message) use ($user) {
            $message->to($user->email)->subject('Your Email has been login successfully');
        });

        return response()->json([
            'user' => new AuthResource($user),
            'status' => new StatusUserResource($userStatus),
            'token' => $token = $user->createToken('auth-token')->plainTextToken
        ], 200);


    }

    public function logout(Request $request)
    {
        // Ensure the user is authenticated
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Update user status to offline
        UserStatus::where('user_id', $request->user()->id)->update([
            'is_online' => false,
            'last_seen_at' => now(),
        ]);

        // Revoke the user's token
        $request->user()->tokens()->delete();

        // Return success response
        return response()->json([
            'message' => 'Logged out successfully',
            'is_online' => false,
            'last_seen_at' => now()->toDateTimeString()
        ]);
    }


    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required| numeric|digits:6'
        ]);


        $user = User::where('email', $request->email)->first();

        if ($user->otp !== $request->otp) {
            return response()->json(['message' => 'this otp is vaild'], 400);
        }


        if (Carbon::now()->gt($user->expired_at)) {
            return response()->json(['message' => 'this otp is expired']);
        }

        $user->email_verified_at = Carbon::now();
        $user->otp = null;
        $user->expired_at = null;
        $user->save();

        return response()->json(['meesage' => 'your Email verified successfully. ']);


    }


}


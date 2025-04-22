<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\AuthResource;
use App\Http\Resources\StatusUserResource;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Http\Requests\RegisterForm;
use App\Models\UserStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Twilio\Rest\Client;

class AuthController extends Controller
{
    public function register(RegisterForm $request)
    {
        try {
            DB::beginTransaction();
            // always use validated to pass valid data to the model
            $data = $request->validated();
            $user = User::create($data);

            $otp = mt_rand(100000, 999999);

            // don't add otp to the database
            // always use cache to store otp
            $user->otp = $otp;
            $user->expired_at = Carbon::now()->addMinutes(5);
            $user->save();

            // use relationship to create user status
            $userStatus = $user->hasStatus()->create([
                'is_online' => true
            ]);


            // $message = "your otp code is " . $otp;
            // $account_sid = env("TWILIO_SID");
            // $auth_token = env("TWILIO_AUTH_TOKEN");
            // $number = env("TWILIO_FROM");
            // $client = new Client($account_sid, $auth_token);
            // $client->Messages->create($data['phone'], [
            //     'from' => $number,
            //     'body' => $message
            // ]);
            DB::commit();
            return response()->json([
                'otp' => 'your Otp Sent to email',
                'user' => new AuthResource($user),
                'status' => new StatusUserResource($userStatus),
                'message' => 'user registerd successfully'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();;
            return response()->json(["message" => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->validated())) {
            return response()->json(['message' => 'invalid email or password'], 401);
        }
        $user = User::where('email', $request->email)->first();
        $userStatus = UserStatus::updateOrCreate(
            ['user_id' => $user->id],
            ['is_online' => true, 'last_seen_at' => null]
        );
        $userStatus->refresh();
        $user->save();
        Mail::send('emails.otp', ['user' => $user], function ($message) use ($user) {
            $message->to($user->email)->subject('Your Email has been login successfully');
        });
        return response()->json([
            'user' => new AuthResource($user),
            'status' => new StatusUserResource($userStatus),
            'token' => $user->createToken('auth-token')->plainTextToken
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

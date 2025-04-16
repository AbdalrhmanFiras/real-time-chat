<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
class OtpController extends Controller
{


    public function sendOtp(Request $request)
    {
        $request->validate(['phone' => 'required']);

        $phoneNumber = $request->phone;
        $factory = (new Factory)->withServiceAccount(config('firebase.credentials'));
        $auth = $factory->createAuth();

        try {
            $signInResult = $auth->signInWithPhoneNumber($phoneNumber);
            return response()->json(['message' => 'OTP sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'id_token' => 'required'
        ]);

        $factory = (new Factory)->withServiceAccount(config('firebase.credentials'));
        $auth = $factory->createAuth();

        try {
            $verifiedToken = $auth->verifyIdToken($request->id_token);
            return response()->json(['message' => 'Phone verified successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid OTP'], 400);
        }
    }

}
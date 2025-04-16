<?php

namespace App\Http\Controllers;

use Kreait\Firebase\Factory;
use Illuminate\Http\Request;
use Kreait\Firebase\Auth;

class FirebaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function sendOtp($phoneNumber)
    {
        $factory = (new Factory)->withServiceAccount(config('/test-7f135-firebase-adminsdk-fbsvc-fdd9a16c9d.json'));
        $auth = $factory->createAuth();

        try {
            $signInResult = $auth->signInWithPhoneNumber($phoneNumber);
            return response()->json(['message' => 'OTP sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function verifyOtp($idToken)
    {
        $factory = (new Factory)->withServiceAccount(config('/test-7f135-firebase-adminsdk-fbsvc-fdd9a16c9d.json'));
        $auth = $factory->createAuth();

        try {
            $verifiedToken = $auth->verifyIdToken($idToken);
            return response()->json(['message' => 'Phone verified successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid OTP'], 400);
        }
    }


    public function __invoke()
    {

    }
    public function index()
    {
        $firebase = (new Factory)->withServiceAccount(__DIR__ . '/test-7f135-firebase-adminsdk-fbsvc-fdd9a16c9d.json')
            ->withDatabaseUri('https://test-7f135-default-rtdb.firebaseio.com');
        $database = $firebase->createDatabase();

        $data = $database->getReference('data');

        return $data->getValue();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

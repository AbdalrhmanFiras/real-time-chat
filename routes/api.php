<?php

use App\Http\Controllers\MyController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Events\MyEvent;


//Route::get('/user', function (Request $request) {
//  return $request->user();
//})->middleware('auth:sanctum');


Route::post('/trigger-event', function (Request $request) {
    // Validate the request
    $validator = Validator::make($request->all(), [
        'message' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => 'Invalid input'], 400);
    }

    $message = $request->input('message');

    // Dispatch the event
    broadcast(new MyEvent($message));

    return response()->json(['status' => 'Event dispatched!']);
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login'])->middleware('email_verified');
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('send-otp', [AuthController::class, 'SendOtp']);
Route::post('verify-otp', [AuthController::class, 'verify']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/Send-message', [MessageController::class, 'Sendmessage']);
    Route::get('/Get-message/{receiver_id}', [MessageController::class, 'Getmessage']);
    Route::post('/Mark-As-Read/{message_id}', [MessageController::class, 'MarkAsRead']);
    Route::post('/Typing-Message', [MessageController::class, 'TypingMessage']);
    Route::put('/update-message/{message_id}', [MessageController::class, 'UpdateMessage']);
    Route::delete('/delete-message/{message_id}', [MessageController::class, 'DeltetMessage']);
});

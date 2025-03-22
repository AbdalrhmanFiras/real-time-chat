<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/Send-message', [MessageController::class, 'Sendmessage']);
    Route::get('/Get-message/{receiver_id}', [MessageController::class, 'Getmessage']);
    Route::post('/Mark-As-Read/{message_id}', [MessageController::class, 'MarkAsRead']);
    Route::post('/Typing-Message', [MessageController::class, 'TypingMessage']);
    Route::put('/update-message/{message_id}', [MessageController::class, 'UpdateMessage']);
    Route::delete('/delete-message/{message_id}', [MessageController::class, 'DeltetMessage']);
});

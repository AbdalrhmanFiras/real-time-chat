<?php
namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckEmailVerifiaction
{
    public function handle(Request $request, Closure $next)
    {
        $user = User::where('email', $request->email)->first();

        // Check if the user is authenticated and their email is verified
        if ($user && is_null($user->email_verified_at)) {
            return response()->json(['message' => 'Please verify your email address first.'], 403);
        }

        // Allow the request to proceed
        return $next($request);
    }
}
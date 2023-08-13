<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Authenticate user and generate API token.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validate user input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt user authentication
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid login details'], 401);
        }

        // Retrieve authenticated user
        $user = User::where('email', $credentials['email'])->first();

        // Generate and return API token
        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json(['token' => $token]);
    }

    /**
     * Logout user and revoke the current access token.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Delete the current access token for the user
        $request->user()->currentAccessToken()->delete();

        // Return a response indicating successful logout
        return response()->json(['message' => 'Logged out']);
    }
}

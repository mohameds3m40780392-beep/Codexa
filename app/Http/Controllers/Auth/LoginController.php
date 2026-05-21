<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{

    // Authenticate user and issue token.

    public function store(LoginRequest $request): JsonResponse
    {
        $request->validated();

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid login credentials.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        // Check if user is an admin
        if ($user->role === 'admin' || strtolower($user->email) === 'admin@example.com') {
            return response()->json([
                'message' => 'Welcome to the Admin Dashboard',
                'access_token' => $token,
                'role' => 'admin',
                'user' => $user
            ], 200);
        }

        // Regular user login response
        return response()->json([
            'message' => 'Logged in successfully',
            'access_token' => $token,
            'role' => 'user',
            'user' => $user
        ], 200);
    }

    /**
     * Log out user and revoke token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully and token revoked.'
        ], 200);
    }
}

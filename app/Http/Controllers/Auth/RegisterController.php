<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    /**
     * Create and register a new user or admin with a verification code.
     */
    public function create(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Check if the chosen role is admin
        if ($data['role'] === 'admin') {
            $secretAdminCode = 'ADMIN_SECRET_2026'; 

            if (empty($data['admin_code']) || $data['admin_code'] !== $secretAdminCode) {
                throw ValidationException::withMessages([
                    'admin_code' => ['The provided management verification code is invalid.'],
                ]);
            }
        }

        // Create the user or admin
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'      => 'Account registered successfully as ' . $user->role,
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user
        ], 201);
    }
}
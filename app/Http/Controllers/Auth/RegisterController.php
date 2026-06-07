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

        // 1. التحقق من كود الأدمن السري إذا تم اختيار صلاحية admin
        if ($data['role'] === 'admin') {
            $secretAdminCode = env('ADMIN_SECRET_CODE');

            if (empty($data['admin_code']) || $data['admin_code'] !== $secretAdminCode) {
                throw ValidationException::withMessages([
                    'admin_code' => ['The provided management verification code is invalid.'],
                ]);
            }
        }

        // 2. الطريقة الجديدة والآمنة لحفظ الصلاحية (role) دون أن يتجاهلها السيرفر
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->role = $data['role']; // هنا سيتم إجبار السيرفر على حفظها كـ admin
        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'      => 'Account registered successfully as ' . $user->role,
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user
        ], 201);
    }
}
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    /**
     * Display the current authenticated user's profile.
     */
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
            'avatar_url' => $request->user()->avatar ? asset('storage/' . $request->user()->avatar) : null
        ], 200);
    }

    /**
     * Update the authenticated user's profile info and avatar.
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle profile avatar upload
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return response()->json([
            'message'    => 'Profile updated successfully.',
            'user'       => $user,
            'avatar_url' => asset('storage/' . $user->avatar)
        ], 200);
    }
}
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController;
use App\Models\User;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
/*

|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Register a new user
Route::post('/register', [RegisterController::class, 'create']);

// Login user and issue token
Route::post('/login', [LoginController::class, 'store']);

/*

|--------------------------------------------------------------------------
| Protected Routes (Sanctum Authentication)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // Log out user and revoke token
    Route::post('/logout', [LoginController::class, 'logout']);

    // Access user dashboard
    Route::get('/dashboard', function () {
        return response()->json([
            'message' => 'Welcome to your dashboard.'
        ], 200);
    });

    // Get any user info by ID
    Route::get('/user/{id}', function ($id) {
        return response()->json([
            'user' => User::findOrFail($id)
        ], 200);
    });

    // View current user profile
    Route::get('/profile', [ProfileController::class, 'show']);

    // Update profile info and avatar
    Route::post('/profile', [ProfileController::class, 'update']);

    // Access protected shopping cart
    Route::get('/cart', function () {
        return response()->json([
            'message' => 'Welcome to your protected shopping cart.'
        ], 200);
    });

    // Access admin control panel
    Route::get('/admin/dashboard', function (Request $request) {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Unauthorized access.'
            ], 403);
        }
        return response()->json([
            'message' => 'Welcome to the Admin control panel.'
        ], 200);
    });
});

// routes/api.php

Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);
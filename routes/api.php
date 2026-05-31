<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController;
use App\Models\User;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartItemsController;
use App\Http\Controllers\Api\OrderController;

/*

|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Register a new user
Route::post('/register', [RegisterController::class, 'create']);

// Login user and issue token
Route::post('/login', [LoginController::class, 'store']);

// Public Store Resources
Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);

// Get any user info by ID (إذا كنت تريدها عامة بدون تسجيل دخول)
Route::get('/user/{id}', function ($id) {
    return response()->json([
        'user' => User::findOrFail($id)
    ], 200);
});


/*

|--------------------------------------------------------------------------
| Protected Routes (Sanctum Authentication)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Auth & Dashboard
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/dashboard', function () {
        return response()->json([
            'message' => 'Welcome to your dashboard.'
        ], 200);
    });

    // User Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'update']);

    // Shopping Cart Actions
    Route::get('/cart', [CartItemsController::class, 'getCart']);
    //Cart
    Route::post('/cart/add', [CartItemsController::class, 'AddToCart']);
    Route::post('/cart/remove', [CartItemsController::class, 'RemoveFromCart']);
    Route::post('/cart/update', [CartItemsController::class, 'UpdateCart']);
    //Checkout
    Route::post('/checkout', [OrderController::class, 'checkout']);

    // Admin Panel
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
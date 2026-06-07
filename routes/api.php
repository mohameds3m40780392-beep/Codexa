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
| Public Routes (متاحة للجميع بدون تسجيل دخول)
|--------------------------------------------------------------------------
*/

// Register & Login
Route::post('/register', [RegisterController::class, 'create']);
Route::post('/login', [LoginController::class, 'store']);

// تصفح المنتجات والأقسام (رؤية فقط)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);

// Get any user info by ID
Route::get('/user/{id}', function ($id) {
    return response()->json([
        'user' => User::findOrFail($id)
    ], 200);
});


/*

|--------------------------------------------------------------------------
| Protected Routes (Sanctum Authentication - تحتاج توكين)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    /*
     * 1. مسارات الـ Admin فقط (ممنوع دخول الـ User العادي)
     */
    // بعد ✅
    Route::group(['middleware' => \App\Http\Middleware\CheckIsAdmin::class], function () {

        // عمليات التحكم بالمنتجات (إضافة، تعديل، حذف)
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);

        // عمليات التحكم بالأقسام (إضافة، تعديل، حذف)
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

        // لوحة تحكم الإدارة
        Route::get('/admin/dashboard', function () {
            return response()->json([
                'message' => 'Welcome to the Admin control panel.'
            ], 200);
        });
    });

    /*
     * 2. مسارات الـ User العادي والـ Admin معاً
     */
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
    Route::post('/cart/add', [CartItemsController::class, 'AddToCart']);
    Route::post('/cart/remove', [CartItemsController::class, 'RemoveFromCart']);
    Route::post('/cart/update', [CartItemsController::class, 'UpdateCart']);

    // Checkout
    Route::post('/checkout', [OrderController::class, 'checkout']);
});
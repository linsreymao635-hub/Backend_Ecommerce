<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{AuthController, ProductController, CategoryController, WishlistController, CartController, OrderController, ReviewController, SocialAuthController};

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Social Login Routes
Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

Route::get('/auth/facebook', [SocialAuthController::class, 'redirectToFacebook']);
Route::get('/auth/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback']);

Route::get('/auth/github', [SocialAuthController::class, 'redirectToGithub']);
Route::get('/auth/github/callback', [SocialAuthController::class, 'handleGithubCallback']);

Route::get('/categories',        [CategoryController::class, 'index']);
Route::get('/categories/{id}',   [CategoryController::class, 'show']);

Route::get('/products',          [ProductController::class, 'index']);
Route::get('/products/{id}',     [ProductController::class, 'show']);
Route::get('/products/{id}/image', [ProductController::class, 'image']);
Route::get('/products/{id}/reviews', [ReviewController::class, 'index']);

// Logout can be called without authentication (token will be invalid anyway)
Route::post('/logout', [AuthController::class, 'logout']);

// Protected routes (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile',              [AuthController::class, 'profile']);
    Route::put('/profile',              [AuthController::class, 'updateProfile']);
    Route::put('/change-password',      [AuthController::class, 'changePassword']);

    Route::get('/wishlist',             [WishlistController::class, 'index']);
    Route::post('/wishlist',            [WishlistController::class, 'store']);
    Route::delete('/wishlist/{id}',     [WishlistController::class, 'destroy']);

    Route::get('/cart',                 [CartController::class, 'index']);
    Route::post('/cart',                [CartController::class, 'store']);
    Route::put('/cart/{id}',            [CartController::class, 'update']);
    Route::delete('/cart/{id}',         [CartController::class, 'destroy']);

    Route::post('/checkout',            [OrderController::class, 'checkout']);
    Route::get('/orders',               [OrderController::class, 'index']);
    Route::get('/orders/{id}',          [OrderController::class, 'show']);

    Route::post('/products/{id}/reviews', [ReviewController::class, 'store']);

    // Admin-only routes
    Route::middleware('admin.api')->group(function () {
        Route::post('/categories',       [CategoryController::class, 'store']);
        Route::put('/categories/{id}',   [CategoryController::class, 'update']);
        Route::delete('/categories/{id}',[CategoryController::class, 'destroy']);

        Route::post('/products',         [ProductController::class, 'store']);
        Route::put('/products/{id}',     [ProductController::class, 'update']);
        Route::delete('/products/{id}',  [ProductController::class, 'destroy']);
    });
});

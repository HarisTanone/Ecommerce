<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::get('login', [LoginController::class, 'AuthLoginRedirect'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
Route::post('register', [RegisterController::class, 'register']);
Route::get('auth', [LoginController::class, 'getAuth']);
Route::apiResource('products', ProductController::class);

Route::middleware('auth')->group(function () {
    Route::get('/cart-view', [CartController::class, 'index'])->name('cart-view');
    Route::post('/cart', [CartController::class, 'addToCart']);
    Route::get('/cart', [CartController::class, 'viewCart']);
    Route::delete('/cart/{itemId}', [CartController::class, 'removeFromCart']);
    Route::post('/checkout', [OrderController::class, 'checkout']);
    Route::get('/success', [OrderController::class, 'success']);
    Route::get('/cancel', [OrderController::class, 'cancel']);
});

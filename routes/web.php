<?php

use Illuminate\Support\Facades\Route;

// Frontend Controllers
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\UserController;
use App\Http\Controllers\Frontend\WishlistController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
*/

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::get('/cart/count', [CartController::class, 'getCount'])->name('cart.count');

// Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index')->middleware('auth');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store')->middleware('auth');
Route::get('/checkout/success/{orderNumber}', [CheckoutController::class, 'success'])->name('checkout.success')->middleware('auth');

// MobilPay Payment Routes
Route::get('/payment/mobilpay/{order_id}', [PaymentController::class, 'showMobilPayPayment'])->name('payment.mobilpay')->middleware('auth');
Route::post('/payment/mobilpay/start', [PaymentController::class, 'startMobilPayPayment'])->name('payment.mobilpay.start')->middleware('auth');
Route::post('/payment/mobilpay/confirm', [PaymentController::class, 'mobilPayConfirm'])->name('payment.mobilpay.confirm');
Route::get('/payment/mobilpay/return', [PaymentController::class, 'mobilPayReturn'])->name('payment.mobilpay.return');

// Auth routes are provided by Laravel Fortify

// User Account
Route::middleware('auth')->group(function () {
    Route::get('/account', [UserController::class, 'index'])->name('account.index');
    Route::get('/account/profile', [UserController::class, 'profile'])->name('account.profile');
    Route::put('/account/profile', [UserController::class, 'updateProfile'])->name('account.profile.update');
    Route::put('/account/password', [UserController::class, 'updatePassword'])->name('account.password.update');
    Route::get('/account/orders', [UserController::class, 'orders'])->name('account.orders');
    Route::get('/account/orders/{id}', [UserController::class, 'showOrder'])->name('account.orders.show');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::post('/wishlist/remove', [WishlistController::class, 'remove'])->name('wishlist.remove');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
});


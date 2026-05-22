<?php

use App\Http\Controllers\Api\ProductController as ApiProductController;
use App\Http\Controllers\Api\CategoryController as ApiCategoryController;
use App\Http\Controllers\Api\OrderController as ApiOrderController;
use Illuminate\Support\Facades\Route;

// API Routes - Public
Route::get('/products', [ApiProductController::class, 'index']);
Route::get('/products/featured', [ApiProductController::class, 'featured']);
Route::get('/products/latest', [ApiProductController::class, 'latest']);
Route::get('/products/{slug}', [ApiProductController::class, 'show']);

Route::get('/categories', [ApiCategoryController::class, 'index']);
Route::get('/categories/{slug}', [ApiCategoryController::class, 'show']);
Route::get('/categories/{slug}/products', [ApiCategoryController::class, 'products']);

// API Routes - Protected (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/orders', [ApiOrderController::class, 'index']);
    Route::get('/orders/{id}', [ApiOrderController::class, 'show']);
    Route::post('/orders', [ApiOrderController::class, 'store']);
});
<?php

use App\Http\Controllers\Backend\BannerController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\HomepageSectionController;
use App\Http\Controllers\Backend\OrderController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\ReceiptController;
use App\Http\Controllers\Backend\SettingsController;
use App\Http\Controllers\Backend\SupplierController;
use App\Http\Controllers\Backend\UserController;
use Illuminate\Support\Facades\Route;

// Admin Routes - All protected by admin middleware
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Products
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::delete('/products/images/{image}', [ProductController::class, 'deleteImage'])->name('products.deleteImage');
    
    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    
    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::put('/orders/{order}/payment', [OrderController::class, 'updatePaymentStatus'])->name('orders.updatePayment');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::post('/orders/{order}/invoice', [OrderController::class, 'generateInvoice'])->name('orders.generateInvoice');
    Route::get('/invoices/{invoice}/download', [OrderController::class, 'downloadInvoice'])->name('invoices.download');
    Route::get('/invoices/{invoice}/send-efactura', [OrderController::class, 'sendToEFactura'])->name('invoices.sendEFactura');
    Route::get('/invoices/{invoice}/download-xml', [OrderController::class, 'downloadXml'])->name('invoices.downloadXml');
    
    // Receipts (NIR)
    Route::get('/receipts', [ReceiptController::class, 'index'])->name('receipts.index');
    Route::get('/receipts/create', [ReceiptController::class, 'create'])->name('receipts.create');
    Route::post('/receipts', [ReceiptController::class, 'store'])->name('receipts.store');
    Route::get('/receipts/{receipt}', [ReceiptController::class, 'show'])->name('receipts.show');
    Route::get('/receipts/{receipt}/download-pdf', [ReceiptController::class, 'downloadPdf'])->name('receipts.downloadPdf');
    Route::delete('/receipts/{receipt}', [ReceiptController::class, 'destroy'])->name('receipts.destroy');
    
    // Suppliers
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
    Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    
    // ANAF API
    Route::post('/anaf/cui', [SupplierController::class, 'searchByCui'])->name('anaf.cui');
    
    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');

    // Settings
    Route::get('/settings/contact', [SettingsController::class, 'editContact'])->name('settings.contact.edit');
    Route::put('/settings/contact', [SettingsController::class, 'updateContact'])->name('settings.contact.update');
    Route::get('/settings/general', [SettingsController::class, 'editGeneral'])->name('settings.general.edit');
    Route::put('/settings/general', [SettingsController::class, 'updateGeneral'])->name('settings.general.update');

    // Banners
    Route::get('/banners', [BannerController::class, 'index'])->name('banners.index');
    Route::get('/banners/create', [BannerController::class, 'create'])->name('banners.create');
    Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
    Route::get('/banners/{banner}', [BannerController::class, 'show'])->name('banners.show');
    Route::get('/banners/{banner}/edit', [BannerController::class, 'edit'])->name('banners.edit');
    Route::put('/banners/{banner}', [BannerController::class, 'update'])->name('banners.update');
    Route::delete('/banners/{banner}', [BannerController::class, 'destroy'])->name('banners.destroy');
    Route::post('/banners/{banner}/toggle-status', [BannerController::class, 'toggleStatus'])->name('banners.toggleStatus');
    Route::post('/banners/update-order', [BannerController::class, 'updateOrder'])->name('banners.updateOrder');

    // Homepage Sections
    Route::get('/homepage-sections', [HomepageSectionController::class, 'index'])->name('homepage-sections.index');
    Route::get('/homepage-sections/create', [HomepageSectionController::class, 'create'])->name('homepage-sections.create');
    Route::post('/homepage-sections', [HomepageSectionController::class, 'store'])->name('homepage-sections.store');
    Route::get('/homepage-sections/{section}', [HomepageSectionController::class, 'show'])->name('homepage-sections.show');
    Route::get('/homepage-sections/{section}/edit', [HomepageSectionController::class, 'edit'])->name('homepage-sections.edit');
    Route::put('/homepage-sections/{section}', [HomepageSectionController::class, 'update'])->name('homepage-sections.update');
    Route::delete('/homepage-sections/{section}', [HomepageSectionController::class, 'destroy'])->name('homepage-sections.destroy');
    Route::post('/homepage-sections/{section}/toggle-status', [HomepageSectionController::class, 'toggleStatus'])->name('homepage-sections.toggleStatus');
    Route::post('/homepage-sections/update-order', [HomepageSectionController::class, 'updateOrder'])->name('homepage-sections.updateOrder');
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\ReportController;

// Auth
Route::get('/',      [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

// Protected
Route::middleware('auth')->group(function () {
    Route::get('/dashboard',   [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/inventory',   [InventoryController::class, 'index'])->name('inventory.index');

    Route::resource('customers', CustomerController::class);
    Route::resource('payments', PaymentController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('products', ProductController::class);
    Route::resource('stock-in', StockInController::class);
    Route::resource('sales',    SalesController::class);
    Route::get('/reports',      [ReportController::class, 'index'])->name('reports.index');
});

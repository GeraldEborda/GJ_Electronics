<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductController;
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

    Route::resource('products', ProductController::class)->except(['show']);
    Route::resource('stock-in', StockInController::class)->except(['edit','update','destroy']);
    Route::resource('sales',    SalesController::class)->except(['edit','update','destroy']);
    Route::get('/reports',      [ReportController::class, 'index'])->name('reports.index');
});
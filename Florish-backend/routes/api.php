<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockAdjustmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('/login', [AuthController::class, 'login'])->name('login');


Route::middleware('auth:sanctum')->group(function () {
    // Group for user management routes
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/user-types', [UserController::class, 'getUserTypes'])->name('user-types.get');
    Route::get('/user', [AuthController::class, 'user'])->name('user');
    Route::prefix('/user')->group(function () {
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::get('{id}', [UserController::class, 'show'])->name('users.show');
        Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });


    // Group for category management routes
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/get-categories', [BrandController::class, 'getCategories'])->name('get-categories.get');
    Route::prefix('/category')->group(function () {
        Route::post('/', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/{id}', [CategoryController::class, 'show'])->name('categories.show');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });


    // Group for brand management routes
    Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
    Route::prefix('/brand')->group(function () {
        Route::post('/', [BrandController::class, 'store'])->name('brands.store');
        Route::get('/{id}', [BrandController::class, 'show'])->name('brands.show');
        Route::put('/{brand}', [BrandController::class, 'update'])->name('brands.update');
        Route::delete('/{id}', [BrandController::class, 'destroy'])->name('brands.destroy');
    });


    // Group for product routes
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::prefix('/product')->group(function () {
        Route::post('/', [ProductController::class, 'store'])->name('products.store');
        Route::put('/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::get('{id}', [ProductController::class, 'show'])->name('products.show');
        //Route for critical-stock
        Route::get('/critical-stocks', [ProductController::class, 'getCriticalStock'])->name('products.critical_stock');
    });


    // Group for stock in routes
    Route::get('/stockIns', [StockInController::class, 'index'])->name('stockIns.index');
    Route::prefix('/stockIn')->group(function () {
        Route::post('/', [StockInController::class, 'store'])->name('stockIns.store');
        Route::get('/generate-reference-number', [StockInController::class, 'generateReferenceNumber'])->name('stockIns.generate_reference_number');
    });


    // Group for stock adjustment routes
    Route::get('/stockAdjustments', [StockAdjustmentController::class, 'index'])->name('stockAdjustments.index');
    Route::prefix('/stockAdjustment')->group(function () {
        Route::post('/', [StockAdjustmentController::class, 'store'])->name('stockAdjustments.store');
    });


    // Logout route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

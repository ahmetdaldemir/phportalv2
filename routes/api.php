<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Mobile App API Routes - Token Authentication
Route::prefix('mobile')->group(function () {
    // Authentication
    Route::post('/login', [App\Http\Controllers\Api\Mobile\AuthController::class, 'login']);
    Route::post('/logout', [App\Http\Controllers\Api\Mobile\AuthController::class, 'logout'])->middleware('auth:sanctum');
    
    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // Serial Number & Stock Check
        Route::post('/stock/check', [App\Http\Controllers\Api\Mobile\StockController::class, 'checkStock']);
        Route::post('/stock/search', [App\Http\Controllers\Api\Mobile\StockController::class, 'searchStock']);
        Route::get('/stock/{id}', [App\Http\Controllers\Api\Mobile\StockController::class, 'getStock']);
        
        // Sales
        Route::post('/sales', [App\Http\Controllers\Api\Mobile\SaleController::class, 'createSale']);
        Route::get('/sales', [App\Http\Controllers\Api\Mobile\SaleController::class, 'getSales']);
        Route::get('/sales/{id}', [App\Http\Controllers\Api\Mobile\SaleController::class, 'getSale']);
        
        // Common Data
        Route::get('/customers', [App\Http\Controllers\Api\Mobile\CommonController::class, 'getCustomers']);
        Route::get('/sellers', [App\Http\Controllers\Api\Mobile\CommonController::class, 'getSellers']);
        Route::get('/categories', [App\Http\Controllers\Api\Mobile\CommonController::class, 'getCategories']);
        Route::get('/brands', [App\Http\Controllers\Api\Mobile\CommonController::class, 'getBrands']);
    });
});

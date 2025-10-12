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

// Common Data API - Centralized endpoints for all shared modules
Route::middleware(['auth', 'companies'])->group(function () {
    // Stock Price API
    Route::get('/stock-price/{id}', [App\Http\Controllers\StockCardController::class, 'getStockPriceApi']);
    Route::get('/customers', [App\Http\Controllers\CustomerController::class, 'getCustomersApi']);
    
    // Common Data API - Cached and optimized
    Route::prefix('common')->group(function () {
        Route::get('/sellers', [App\Http\Controllers\Api\CommonDataController::class, 'getSellers']);
        Route::get('/categories', [App\Http\Controllers\Api\CommonDataController::class, 'getCategories']);
        Route::get('/warehouses', [App\Http\Controllers\Api\CommonDataController::class, 'getWarehouses']);
        Route::get('/colors', [App\Http\Controllers\Api\CommonDataController::class, 'getColors']);
        Route::get('/brands', [App\Http\Controllers\Api\CommonDataController::class, 'getBrands']);
        Route::get('/versions', [App\Http\Controllers\Api\CommonDataController::class, 'getVersions']);
        Route::get('/reasons', [App\Http\Controllers\Api\CommonDataController::class, 'getReasons']);
        Route::get('/customers', [App\Http\Controllers\Api\CommonDataController::class, 'getCustomers']);
        Route::get('/cities', [App\Http\Controllers\Api\CommonDataController::class, 'getCities']);
        Route::get('/towns', [App\Http\Controllers\Api\CommonDataController::class, 'getTowns']);
        Route::get('/currencies', [App\Http\Controllers\Api\CommonDataController::class, 'getCurrencies']);
        Route::get('/safes', [App\Http\Controllers\Api\CommonDataController::class, 'getSafes']);
        Route::get('/users', [App\Http\Controllers\Api\CommonDataController::class, 'getUsers']);
        
        // Bulk data endpoint
        Route::get('/all', [App\Http\Controllers\Api\CommonDataController::class, 'getAllCommonData']);
        
        // Cache management
        Route::post('/clear-cache', [App\Http\Controllers\Api\CommonDataController::class, 'clearCache']);
    });
});


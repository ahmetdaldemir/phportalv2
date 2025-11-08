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

// NOTE: Most API routes moved to web.php for session-based authentication
// Only keep routes here that truly need token-based auth (Sanctum)

// Common Data API - Centralized endpoints for all shared modules
// MOVED TO WEB.PHP for session support
// Route::middleware(['auth', 'companies'])->group(function () {
//     Route::get('/stock-price/{id}', [App\Http\Controllers\StockCardController::class, 'getStockPriceApi']);
//     Route::get('/customers', [App\Http\Controllers\CustomerController::class, 'getCustomersApi']);
//     Route::get('/stock/check', [App\Http\Controllers\HomeController::class, 'checkStock']);
// });


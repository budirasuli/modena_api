<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\ServiceCenterController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\TradeInController;
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

// Product
Route::get('/product', [ProductController::class, 'index']);
Route::get('/detail', [ProductController::class, 'detail']);
Route::get('/sku', [ProductController::class, 'sku']);

// Address
Route::get('/address', [AddressController::class, 'index']);
Route::get('/address_detail', [AddressController::class, 'address_detail']);
Route::post('/address', [AddressController::class, 'store']);
Route::get('/address/{id}', [AddressController::class, 'edit']);
Route::put('/address/{id}', [AddressController::class, 'update']);
Route::delete('/address/{id}',[AddressController::class, 'destroy']);

// Service
Route::get('/service', [ServiceCenterController::class, 'index']);
Route::post('/service', [ServiceCenterController::class, 'store']);

// Rental
Route::get('/rental', [RentalController::class, 'index']);
Route::post('/rental', [RentalController::class, 'store']);

// Trade In
Route::get('/tradein', [TradeInController::class, 'index']);
Route::post('/tradein', [TradeInController::class, 'store']);

// FAQ
Route::get('/faq', [FaqController::class, 'index']);
Route::get('/faq-detail/{id}', [FaqController::class, 'faq_detail']);

// Store Location
Route::get('/online-store', [StoreController::class, 'online_store']);
Route::get('/store-location', [StoreController::class, 'store_location']);

<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\ServiceCenterController;
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
Route::get('/service/{id}', [ServiceCenterController::class, 'edit']);
Route::put('/service/{id}', [ServiceCenterController::class, 'update']);
Route::delete('/service/{id}',[ServiceCenterController::class, 'destroy']);

// Trade In
Route::get('/tradein', [ServiceCenterController::class, 'index']);
Route::post('/tradein', [ServiceCenterController::class, 'store']);
Route::get('/tradein/{id}', [ServiceCenterController::class, 'edit']);
Route::put('/tradein/{id}', [ServiceCenterController::class, 'update']);
Route::delete('/tradein/{id}',[ServiceCenterController::class, 'destroy']);






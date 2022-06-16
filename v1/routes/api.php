<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\ManualBookController;
use App\Http\Controllers\ProductRegistrationController;
use App\Http\Controllers\ServiceCenterController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\TradeInController;
use App\Http\Controllers\UserRegisterController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ClaimCashbackController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Product
Route::get('/product', [ProductController::class, 'index']);
Route::get('/product_category', [ProductController::class, 'product_category']);
Route::get('/product_sub_category', [ProductController::class, 'product_sub_category']);
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

// Manual Book Download
Route::get('/manual-book/{id}', [ManualBookController::class, 'index']);
Route::get('/manual-book-download/{id}', [ManualBookController::class, 'download']);

// User
Route::get('/user_profile', [UserRegisterController::class, 'index']);
Route::post('/user_register', [UserRegisterController::class, 'register']);

// Product Registration
Route::get('/product_register', [ProductRegistrationController::class, 'index']);
Route::post('/product_register', [ProductRegistrationController::class, 'store']);

// Location
Route::get('/province', [LocationController::class, 'index']);
Route::get('/city', [LocationController::class, 'city']);
Route::get('/district', [LocationController::class, 'district']);
Route::get('/subdistrict', [LocationController::class, 'subdistrict']);
Route::get('/postalcode', [LocationController::class, 'postalcode']);

// Brand
Route::get('/brand', [BrandController::class, 'index']);

// Cashback
Route::get('/claim-cashback', [ClaimCashbackController::class, 'index']);
Route::post('/claim-cashback', [ClaimCashbackController::class, 'store']);

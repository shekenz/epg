<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CouponsController;
use App\Http\Controllers\BooksController;
use App\Http\Controllers\VariationsController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\FilterOrderController;
use App\Http\Controllers\ShippingMethodsController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
	return $request->user();
});

//TODO secure the api

Route::middleware('shop')->group(function() {

	// Live cart
	Route::prefix('cart')->name('cart.api')->group(function() {
		Route::post('/add/{book}', [CartController::class, 'add'])->name('.add');
		Route::post('/remove/{book}', [CartController::class, 'remove'])->name('.remove');
		Route::post('/remove-all/{book}', [CartController::class, 'removeAll'])->name('.removeAll');
		Route::post('/check', [CartController::class, 'checkCart'])->name('.check');
	});

	// Order (Paypal)
	Route::prefix('order')->group(function() {
		Route::post('/create/{shippingMethod}/{couponID}', [OrdersController::class, 'createOrder']);
		Route::post('/check-country/{countryCode}', [OrdersController::class, 'checkCountry']);
		Route::post('/cancel/{order}', [OrdersController::class, 'cancel']);
		Route::post('/details/{orderID}', [OrdersController::class, 'details']);
		Route::post('/capture/{orderID}', [OrdersController::class, 'capture']);
	});

	// Coupon
	Route::post('/coupon/get/{couponLabel}', [CouponsController::class, 'get']);

});

// Backend API
Route::get('shippings/{shippingMethod}', [ShippingMethodsController::class, 'show']);
Route::post('orders/filter', [FilterOrderController::class, 'filter']);
Route::apiResource('orders', OrderController::class)->only(['index', 'show']);

Route::post('/books/reorder', [BooksController::class, 'reorder']);
Route::post('/variations/{bookInfo}/reorder', [VariationsController::class, 'reorder']);
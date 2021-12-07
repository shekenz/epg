<?php

use App\Http\Controllers\BooksController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ShippingMethodsController;
use App\Http\Controllers\CouponsController;
use App\Http\Controllers\OrdersMassController;
use App\Http\Controllers\ArchivedOrdersController;
use App\Http\Controllers\PriceStopsController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\VariationsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('published')->group(function() {

	// Main index route
	Route::get('/', [BooksController::class, 'index'])->name('index');
	Route::get('/about', [IndexController::class, 'about'])->name('about');
	Route::get('/terms', [IndexController::class, 'terms'])->name('terms');
	Route::view('/contact', 'index.contact')->name('contact');
	Route::post('/contact', [MessagesController::class, 'forward'])->name('messages.forward');

	// Cart
	Route::middleware('shop')->prefix('cart')->name('cart')->group(function() {
		Route::get('/', [CartController::class, 'viewCart']);
		Route::get('/clear', [CartController::class, 'clearCart'])->name('.clear');
		Route::get('/success', [CartController::class, 'success'])->name('.success');
		Route::view('/checkout', 'index.cart.shipping')->name('.checkout');
	});

});

Route::middleware('auth')->prefix('dashboard')->group(function() {

	// Dashboard
	Route::get('/', function () {
		return view('dashboard');
	})->name('dashboard');

	// Orders
	Route::name('orders')->group(function() {
		Route::get('/orders', [OrdersController::class, 'list']);
		Route::get('/order/{id}', [OrdersController::class, 'display'])->name('.display');
		Route::get('/order/cancel/{order}', [OrdersController::class, 'cancel'])->name('.cancel');
		Route::get('/order/recycle/{orderID}', [OrdersController::class, 'recycle'])->name('.recycle');
		Route::post('/order/shipped/{orderID}', [OrdersController::class, 'shipped'])->name('.shipped');
		Route::get('/orders/hidden', [OrdersController::class, 'hidden'])->name('.hidden');
	});
	
	Route::prefix('orders')->group(function() {

		// Orders mass process
		Route::name('orders')->group(function() {
			Route::post('/csv', [OrdersMassController::class, 'csv'])->name('.csv');
			Route::post('/hide', [OrdersMassController::class, 'hide'])->name('.hide');
			Route::post('/unhide', [OrdersMassController::class, 'unhide'])->name('.unhide');

			Route::prefix('print')->group(function() {
				Route::post('/{view}', [OrdersMassController::class, 'pdf'])->name('.print');
				Route::post('/labels/preview', [OrdersMassController::class, 'labelsPreview'])->name('.labelsPreview');
				Route::post('/labels/{extra?}', [OrdersMassController::class, 'labels'])->name('.labels');
			});
		});

		// Archived Orders
		Route::name('archive')->group(function() {
			Route::post('/archive/{order}', [ArchivedOrdersController::class, 'archive'])->name('.order');
			Route::get('/archived/{archivedOrder}', [ArchivedOrdersController::class, 'display'])->name('.display');
			Route::get('/archived', [ArchivedOrdersController::class, 'list'])->name('.list');
		});

	});

	// Shipping methods
	Route::prefix('shipping-methods')->name('shippingMethods')->group(function() {
		Route::post('/add', [ShippingMethodsController::class, 'add'])->name('.add');
		Route::post('/add-stop/{shippingMethod}', [PriceStopsController::class, 'add'])->name('.addStop');
		// TODO Should be post or delete method, but I'm too lazy to create a form in the blade view
		Route::get('/delete-stop/{priceStop}', [PriceStopsController::class, 'delete'])->name('.deleteStop');
		// TODO Should be post or delete method, but I'm too lazy to create a form in the blade view
		Route::get('/delete/{shippingMethod}', [ShippingMethodsController::class, 'delete'])->name('.delete');
		Route::get('/edit/{shippingMethod}', [ShippingMethodsController::class, 'edit'])->name('.edit');
		Route::patch('/edit/{shippingMethod}', [ShippingMethodsController::class, 'update'])->name('.update');
	});

	// Users
	Route::name('users')->group(function() {
		Route::get('/users', [UsersController::class, 'list']);
		Route::get('/user/{user}', [UsersController::class, 'display'])->name('.display');
		Route::get('/user/edit/{user}', [UsersController::class, 'edit'])->name('.edit');
		Route::patch('/user/{user}', [UsersController::class, 'update'])->name('.update');
		Route::post('/user/delete/{user}', [UsersController::class, 'delete'])->name('.delete');
		Route::get('/users/invite', [UsersController::class, 'invitation'])->name('.invitation');
		Route::post('/users/invite', [UsersController::class, 'invite'])->name('.invite');
	});

	// Books
	Route::prefix('books')->name('books')->group(function() {

		Route::prefix('archives')->name('.archives')->group(function() {
			Route::get('/', [BooksController::class, 'archived']);
			Route::get('/{bookInfo}', [BooksController::class, 'archive'])->name('.store');
			Route::post('/delete/all', [BooksController::class, 'deleteAll'])->name('.delete.all');
			Route::post('/delete/{bookInfo}', [BooksController::class, 'delete'])->name('.delete');
			Route::get('/restore/{id}', [BooksController::class, 'restore'])->name('.restore');
		});
		
		Route::get('/', [BooksController::class, 'list']);
		Route::get('/create', [BooksController::class, 'create'])->name('.create');
		Route::post('/', [BooksController::class, 'store'])->name('.store');
		Route::get('/edit/{bookInfo}', [BooksController::class, 'edit'])->name('.edit');
		Route::patch('/edit/{bookInfo}', [BooksController::class, 'update'])->name('.update');
		Route::get('/{bookInfo}', [BooksController::class, 'display'])->name('.display');
		Route::post('/delete/{id}', [BooksController::class, 'delete'])->name('.delete');
	});

	//Variation
	Route::prefix('books/variations')->name('variations')->group(function() {
		Route::get('/{bookInfo}/add', [VariationsController::class, 'create'])->name('.create');
		Route::post('/{bookInfo}/add', [VariationsController::class, 'store'])->name('.store');
		Route::get('/restore/{id}', [VariationsController::class, 'restore'])->name('.restore');
		Route::post('/refresh/{id}', [VariationsController::class, 'refresh'])->name('.refresh');
		Route::get('/edit/{book}', [VariationsController::class, 'edit'])->name('.edit');
		Route::patch('/edit/{book}', [VariationsController::class, 'update'])->name('.update');
		Route::delete('/delete/{book}', [VariationsController::class, 'delete'])->name('.delete');
	});

	// Media
	Route::prefix('media')->name('media')->group(function() {
		Route::get('/', [MediaController::class, 'list']);
		Route::post('/', [MediaController::class, 'store'])->name('.store');
		Route::get('/create', [MediaController::class, 'create'])->name('.create');
		Route::get('/refresh', [MediaController::class, 'refreshAll'])->name('.optimize.refreshAll');
		Route::get('/rebuild', [MediaController::class, 'rebuildAll'])->name('.optimize.rebuildAll');
		Route::get('/{medium}', [MediaController::class, 'display'])->name('.display');
		Route::patch('/{medium}', [MediaController::class, 'update'])->name('.update');
		Route::get('/refresh/{medium}', [MediaController::class, 'refresh'])->name('.optimize.refresh');
		Route::get('/rebuild/{medium}', [MediaController::class, 'rebuild'])->name('.optimize.rebuild');
		Route::get('/{medium}/break/{book}', [MediaController::class, 'breakLink'])->name('.break');
		Route::delete('/delete/{id}', [MediaController::class, 'delete'])->name('.delete');
	});

	// Settings
	Route::prefix('settings')->group(function() {

		// Coupons
		Route::prefix('coupon')->name('coupons')->group(function() {
			Route::post('/add', [CouponsController::class, 'add'])->name('.add');
			// TODO Should be post or delete method, but I'm too lazy to create a form in the blade view
			Route::get('/delete/{coupon}', [CouponsController::class, 'delete'])->name('.delete');
		});

		// Settings
		Route::name('settings')->group(function() {

			Route::get('/', [SettingsController::class, 'main']);
			Route::patch('/', [SettingsController::class, 'update'])->name('.update');
			Route::post('/publish', [SettingsController::class, 'publish'])->name('.publish');
			Route::post('/toggleshop', [SettingsController::class, 'toggleShop'])->name('.toggleShop');

			// Acronyms
			Route::prefix('acronyms')->group(function() {
				Route::post('/add', [SettingsController::class, 'addAcronym'])->name('.addAcronym');
				// TODO Should be post or delete method, but I'm too lazy to create a form in the blade view
				Route::get('/delete/{acronym}', [SettingsController::class, 'deleteAcronym'])->name('.deleteAcronym');
			});
			
		});
	});

	// Clients
	Route::get('/clients', [ClientsController::class, 'list'])->name('clients');
	Route::get('/export', [ClientsController::class, 'csv'])->name('clients.export');

	// Misc/Debug/Log
	Route::get('/mails/log', [MessagesController::class, 'log'])->name('mails.log');
	Route::get('/phpinfo', function() {
		return view('other.phpinfo');
	})->name('phpinfo');

});

require __DIR__.'/auth.php';

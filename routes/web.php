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

// Main index route
Route::get('/', [BooksController::class, 'index'])->middleware('published')->name('index');
Route::get('/about', [IndexController::class, 'about'])->middleware('published')->name('about');
Route::view('/contact', 'index.contact')->middleware('published')->name('contact');
Route::post('/contact', [MessagesController::class, 'forward'])->middleware('published')->name('messages.forward');
Route::get('/order/{orderID}', [OrdersController::class, 'index'])->middleware(['published', 'shop'])->name('orders.index');

// Cart
Route::get('/cart', [CartController::class, 'viewCart'])->middleware(['published', 'shop'])->name('cart');
Route::get('/cart/clear', [CartController::class, 'clearCart'])->middleware(['published', 'shop'])->name('cart.clear');
Route::get('/cart/success', [CartController::class, 'success'])->middleware(['published', 'shop'])->name('cart.success');
Route::view('/cart/checkout', 'index.cart.shipping')->middleware(['published', 'shop'])->name('cart.checkout');

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

// Orders
Route::get('/dashboard/orders/', [OrdersController::class, 'list'])->middleware('auth')->name('orders');
Route::get('/dashboard/order/{id}', [OrdersController::class, 'display'])->middleware('auth')->name('orders.display');
Route::get('/dashboard/order/cancel/{order}', [OrdersController::class, 'cancel'])->middleware('auth')->name('orders.cancel');
Route::get('/dashboard/order/recycle/{orderID}', [OrdersController::class, 'recycle'])->middleware('auth')->name('orders.recycle');
Route::post('/dashboard/order/shipped/{orderID}', [OrdersController::class, 'shipped'])->middleware('auth')->name('orders.shipped');
Route::get('/dashboard/orders/hidden', [OrdersController::class, 'hidden'])->middleware('auth')->name('orders.hidden');

// Archived Orders
Route::post('/dashboard/order/archive/{order}', [ArchivedOrdersController::class, 'archive'])->middleware('auth')->name('archive.order');
Route::get('/dashboard/order/archived/{archivedOrder}', [ArchivedOrdersController::class, 'display'])->middleware('auth')->name('archive.display');
Route::get('/dashboard/orders/archived', [ArchivedOrdersController::class, 'list'])->middleware('auth')->name('archive.list');

// Orders mass process
Route::post('/dashboard/orders/csv', [OrdersMassController::class, 'csv'])->middleware('auth')->name('orders.csv');
Route::post('/dashboard/orders/hide', [OrdersMassController::class, 'hide'])->middleware('auth')->name('orders.hide');
Route::post('/dashboard/orders/unhide', [OrdersMassController::class, 'unhide'])->middleware('auth')->name('orders.unhide');
Route::post('/dashboard/orders/print/{view}', [OrdersMassController::class, 'pdf'])->middleware('auth')->name('orders.print');
Route::post('/dashboard/orders/print/labels/preview', [OrdersMassController::class, 'labelsPreview'])->middleware('auth')->name('orders.labelsPreview');
Route::post('/dashboard/orders/print/labels/{extra?}', [OrdersMassController::class, 'labels'])->middleware('auth')->name('orders.labels');

// Shipping methods
Route::post('/dashboard/shipping-methods/add/', [ShippingMethodsController::class, 'add'])->middleware('auth')->name('shippingMethods.add');
Route::get('/dashboard/shipping-methods/delete/{shippingMethod}', [ShippingMethodsController::class, 'delete'])->middleware('auth')->name('shippingMethods.delete');

// Coupons
Route::post('/dashboard/coupon/add/', [CouponsController::class, 'add'])->middleware('auth')->name('coupons.add');
Route::get('/dashboard/coupon/delete/{coupon}', [CouponsController::class, 'delete'])->middleware('auth')->name('coupons.delete');

// Users
Route::get('/dashboard/users', [UsersController::class, 'list'])->middleware('auth')->name('users');
Route::get('/dashboard/user/{user}', [UsersController::class, 'display'])->middleware('auth')->name('users.display');
Route::get('/dashboard/user/edit/{user}', [UsersController::class, 'edit'])->middleware('auth')->name('users.edit');
Route::patch('/dashboard/user/{user}', [UsersController::class, 'update'])->middleware('auth')->name('users.update');
Route::post('/dashboard/user/delete/{user}', [UsersController::class, 'delete'])->middleware('auth')->name('users.delete');
Route::get('/dashboard/users/invite', [UsersController::class, 'invitation'])->middleware('auth')->name('users.invitation');
Route::post('/dashboard/users/invite', [UsersController::class, 'invite'])->middleware('auth')->name('users.invite');

// Books
Route::get('/dashboard/books', [BooksController::class, 'list'])->middleware('auth')->name('books');
Route::get('/dashboard/book/create', [BooksController::class, 'create'])->middleware('auth')->name('books.create');
Route::post('/dashboard/books', [BooksController::class, 'store'])->middleware('auth')->name('books.store');
Route::get('/dashboard/book/edit/{id}', [BooksController::class, 'edit'])->middleware('auth')->name('books.edit');
Route::patch('/dashboard/book/{book}', [BooksController::class, 'update'])->middleware('auth')->name('books.update');
Route::get('/dashboard/book/{id}', [BooksController::class, 'display'])->middleware('auth')->name('books.display');
Route::get('/dashboard/book/archive/{book}', [BooksController::class, 'archive'])->middleware('auth')->name('books.archive');
Route::post('/dashboard/book/delete/{id}', [BooksController::class, 'delete'])->middleware('auth')->name('books.delete');
Route::post('/dashboard/books/archived/delete', [BooksController::class, 'deleteAll'])->middleware('auth')->name('books.deleteAll');
Route::get('/dashboard/book/restore/{id}', [BooksController::class, 'restore'])->middleware('auth')->name('books.restore');
Route::get('/dashboard/books/archived', [BooksController::class, 'archived'])->middleware('auth')->name('books.archived');

// Media
Route::get('/dashboard/media', [MediaController::class, 'list'])->middleware('auth')->name('media');
Route::post('/dashboard/media', [MediaController::class, 'store'])->middleware('auth')->name('media.store');
Route::get('/dashboard/media/create', [MediaController::class, 'create'])->middleware('auth')->name('media.create');
Route::get('/dashboard/media/refresh', [MediaController::class, 'refreshAll'])->middleware('auth')->name('media.optimize.refreshAll');
Route::get('/dashboard/media/rebuild', [MediaController::class, 'rebuildAll'])->middleware('auth')->name('media.optimize.rebuildAll');
Route::get('/dashboard/media/{medium}', [MediaController::class, 'display'])->middleware('auth')->name('media.display');
Route::patch('/dashboard/media/{medium}', [MediaController::class, 'update'])->middleware('auth')->name('media.update');
Route::get('/dashboard/media/refresh/{medium}', [MediaController::class, 'refresh'])->middleware('auth')->name('media.optimize.refresh');
Route::get('/dashboard/media/rebuild/{medium}', [MediaController::class, 'rebuild'])->middleware('auth')->name('media.optimize.rebuild');
Route::get('/dashboard/media/{medium}/break/{book}', [MediaController::class, 'breakLink'])->middleware('auth')->name('media.break');
Route::post('/dashboard/media/delete/{id}', [MediaController::class, 'delete'])->middleware('auth')->name('media.delete');

// Settings
Route::get('/dashboard/settings', [SettingsController::class, 'main'])->middleware('auth')->name('settings');
Route::patch('/dashboard/settings', [SettingsController::class, 'update'])->middleware('auth')->name('settings.update');
Route::post('/dashboard/settings/publish', [SettingsController::class, 'publish'])->middleware('auth')->name('settings.publish');
Route::post('/dashboard/settings/toggleshop', [SettingsController::class, 'toggleShop'])->middleware('auth')->name('settings.toggleShop');

// Misc/Debug/Log
Route::get('/dashboard/mails/log', [MessagesController::class, 'log'])->middleware('auth')->name('mails.log');
Route::get('/dashboard/phpinfo', function() {
	return view('other.phpinfo');
})->middleware('auth')->name('phpinfo');

require __DIR__.'/auth.php';

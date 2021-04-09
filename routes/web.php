<?php

use App\Http\Controllers\BooksController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\FrontController;

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
//Route::get('/', [FrontController::class, 'index'])->name('index');

Route::get('/', [BooksController::class, 'index'])->name('index');
Route::view('/cart', 'index/cart', [
	'subTotal' => '240',
	'artist' => 'Name',
	'title' => 'Title',
	'quantity' => '1',
])->name('cart');
Route::view('/about', 'index/about')->name('about');
Route::view('/contact', 'index/contact')->name('contact');

// Loggin route


// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

// Users (Auth in controller)
Route::get('/dashboard/users', [UsersController::class, 'list'])->middleware('auth')->name('users');
Route::get('/dashboard/user/{user}', [UsersController::class, 'display'])->middleware('auth')->name('users.display');
Route::get('/dashboard/user/edit/{user}', [UsersController::class, 'edit'])->middleware('auth')->name('users.edit');
Route::patch('/dashboard/user/{user}', [UsersController::class, 'update'])->middleware('auth')->name('users.update');
Route::post('/dashboard/user/delete/{user}', [UsersController::class, 'delete'])->middleware('auth')->name('users.delete');

// Books (Auth in controller)
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
Route::get('/dashboard/media/{medium}', [MediaController::class, 'display'])->middleware('auth')->name('media.display');
Route::patch('/dashboard/media/{medium}', [MediaController::class, 'update'])->middleware('auth')->name('media.update');
Route::get('/dashboard/media/{medium}/break/{book}', [MediaController::class, 'breakLink'])->middleware('auth')->name('media.break');
Route::post('/dashboard/media/delete/{id}', [MediaController::class, 'delete'])->middleware('auth')->name('media.delete');

require __DIR__.'/auth.php';

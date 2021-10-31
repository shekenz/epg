<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\BookInfo;
use App\Models\Medium;
use App\Traits\MediaManager;

/**
 * Controller for the Books Library
 */
class BooksController extends Controller
{

	use MediaManager;



	/** @var array $validation contains the validation rules for creating or updating a book */
	protected $bookValidation = [
		'label' => ['required', 'max:128'],
		'weight' => ['required', 'min:0', 'integer'],
		'stock' => ['required', 'integer'],
		'pre_order' => ['nullable', 'boolean'],
		'price' => ['required', 'min:0', 'numeric'],
		'media' => ['nullable', 'array'],
		'files.*' => ['nullable', 'file', 'mimes:jpg,gif,png'],
	];

	protected $infoValidation = [
		'title' => ['required', 'string', 'max:128'],
		'author' => ['nullable', 'string', 'max:64'],
		'position' => ['nullable', 'integer'],
		'width' => ['nullable', 'integer'],
		'height' => ['nullable', 'integer'],
		'pages' => ['nullable', 'integer'],
		'cover' => ['nullable', 'string', 'max:32'],
		'copies' => ['nullable', 'integer'],
		'year' => ['nullable', 'integer', 'digits_between:0,4'],
		'description' => ['required', 'string'],
	];



	/** Lists all books from the library for the frontend index. Filters out books with no linked media. */
  public function index() {
		// We need to filter out the books without linked images because gilde.js hangs if it has no child elements.
		// We also need a clean ordered index to link each glides to its corresponding counter.
		$bookInfos = BookInfo::with([
			'books'
		])
		->orderBy('id', 'DESC')
		->get();
		
		return view('books/index', compact('bookInfos'));
	}



	/** Lists all books from the library. Index of the books library in backend. */
	public function list() {
		$bookInfos = BookInfo::orderBy('id', 'DESC')->get();
		//$archived = Book::onlyTrashed()->count();
      return view('books/list', compact('bookInfos'));
	}


	
	/** Displays the book resume in backend. */
	public function display(BookInfo $bookInfo) {
		return view('books.display', compact('bookInfo'));
	}



	/** Displays new book creation page. */
	public function create() {
		$media = Medium::all();
		return view('books/create', compact('media'));
	}



	/** Create a new book in the database and links it with provided media. */
	public function store(Request $request) {
		$bookData = $request->validate($this->bookValidation);
		$infoData = $request->validate($this->infoValidation);
		$mediaIDs = array(); // Array containing all media ids to attach to the new book

		// Storing all uploaded images
		if(array_key_exists('files', $bookData)) {
			foreach($bookData['files'] as $file) {
				// Pushing new files ids for attachment
				array_push($mediaIDs, self::storeMedia($file));
			}
		}

		// Merging files uploaded and files from library together for attachment
		if(array_key_exists('media', $bookData)) {
			// $bookData['media'] is also an array of ids
			$mediaIDs = array_merge($bookData['media'], $mediaIDs);
		}

		// Saving book in database
		// Creating bookInfo from user/bookInfo model relationship to insert the user_id
		$bookInfo = auth()->user()->bookInfos()->create($infoData);
		// Creating book from bookInfo/book model relationship to insert the book_info_id
		$book = $bookInfo->books()->create($bookData);
		
		/** 
		 * Creating a new array with ids as key, and a second array [ 'order' => position ] as value that holds media position (In the pivot table)
		 * According to documentation : "For convenience, attach and detach also accept arrays of IDs as input"
		 * https://laravel.com/docs/8.x/eloquent-relationships#updating-many-to-many-relationships
		 * $mediaIDsWithOrder = [
		 * 		int:media_id => [ 'order' => int:position ]
		 * ]
		 */
		$mediaIDsWithOrder = [];
		foreach($mediaIDs as $order => $id) {
			$mediaIDsWithOrder[$id] = ['order' => $order];
		}

		// Attach
		if(!empty($mediaIDs)) {
			$book->media()->attach($mediaIDsWithOrder);
		}

		return redirect()->route('books')->with([
			'flash' => __('flash.book.added'),
			'flash-type' => 'success'
		]);
	}



	/** Displays the book edition page. */
	public function edit(BookInfo $bookInfo) {
		$media = Medium::all();
		return view('books/edit', compact('bookInfo', 'media'));
	}



	/** Updates the book's info and re-links media if necessary. */
	public function update(Request $request, BookInfo $bookInfo) {

		$data = $request->validate($this->infoValidation);

		// Updating book
		$bookInfo->update($data);
		
		return redirect()->route('books')->with([
			'flash' => __('flash.book.updated'),
			'flash-type' => 'success'
		]);
	}



	// Lists all archived books. Index of archives in backend.
	public function archived() {
		$books = Book::onlyTrashed()->get();
		$archived = Book::onlyTrashed()->count();
		return view('books/archived', compact('books', 'archived'));
	}



	// Archives a book (SoftDelete)
	public function archive(Book $book) {
		$book->delete();
		return redirect()->route('books')->with([
			'flash' => __('flash.book.archived'),
			'flash-type' => 'info'
		]);
	}



	// Restore a book from archives to library.
	public function restore($id) {
		// Can't bind a deleted model, will throw a 404
		Book::onlyTrashed()->findOrFail($id)->restore();
		return redirect()->route('books.archived')->with([
			'flash' => __('flash.book.restored'),
			'flash-type' => 'success'
		]);
	}



	// Permanently deletes a book from archives.
	public function delete($id) {
		// Can't bind a deleted model, will throw a 404
		$book = Book::with(['media', 'orders'])->onlyTrashed()->findOrFail($id);

		// Check if book is still attached to an order
		if($book->orders->isEmpty()) {
			$book->media()->detach();
			$book->forceDelete();
			return redirect()->route('books.archived')->with([
				'flash' => __('flash.book.deleted'),
				'flash-type' => 'success'
			]);
		} else {
			return redirect()->route('books.archived')->with([
				'flash' => __('flash.book.still-linked'),
				'flash-type' => 'error'
			]);
		}
	}



	// Permanently deletes ALL books from archives.
	public function deleteAll() {
		$books = Book::with(['media', 'orders'])->onlyTrashed()->get();
		$booksForDeletion = $books->filter(function($book) {
			return $book->orders->isEmpty();
		});

		$booksNotDeleted = $books->diff($booksForDeletion);
		$booksForDeletion->each(function($book) {
			$book->media()->detach();
			$book->forceDelete();
		});

		if($booksNotDeleted->isEmpty()) {
			return redirect()->route('books')->with([
				'flash' => __('flash.book.all-deleted'),
				'flash-type' => 'success'
			]);
		} else {
			return redirect()->back()->with([
				'flash' => __('flash.book.some-still-linked'),
				'flash-type' => 'error'
			]);
		}
	}



}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Medium;
use App\Traits\MediaManager;

/**
 * Controller for the Books Library
 */
class BooksController extends Controller
{
	use MediaManager;

	/** @var array $validation contains the validation rules for creating or updating a book */
	protected $validation = [
		'title' => ['required', 'string', 'max:128'],
		'author' => ['nullable', 'string', 'max:64'],
		'width' => ['nullable', 'integer'],
		'height' => ['nullable', 'integer'],
		'pages' => ['nullable', 'integer'],
		'cover' => ['nullable', 'string', 'max:32'],
		'weight' => ['required', 'integer', 'min:0'],
		'copies' => ['nullable', 'integer'],
		'quantity' => ['required', 'integer'],
		'pre_order' => ['nullable', 'boolean'],
		'year' => ['nullable', 'integer', 'digits_between:0,4'],
		'price' => ['nullable', 'numeric'],
		'description' => ['required', 'string'],
		'files.*' => ['nullable', 'file', 'mimes:jpg,gif,png'],
		'media' => ['nullable', 'array'],
	];

	public function __contruct() {
    }

	/** Lists all books from the library for the frontend index. Filters out books with no linked media. */
    public function index() {
		// We need to filter out the books without linked images because gilde.js hangs if it have no child elements.
		// We also need a clean ordered index to link each glides to its corresponding counter.
		$books = Book::with([
				'media' => function($q) { $q->orderBy('pivot_order', 'asc'); }
			])
			->orderBy('created_at', 'DESC')
			->get()
			->filter(function($value) {
				return $value->media->isNotEmpty();
			})
			->values();
        return view('books/index', compact('books'));
	}

	/** Lists all books from the library. Index of the books library in backend. */
	public function list() {
		$books = Book::orderBy('created_at', 'DESC')->get();
		$archived = Book::onlyTrashed()->count();
        return view('books/list', compact('books', 'archived'));
	}
	
	/** Displays the book resume in backend. */
	public function display($id) {
		$book = Book::with([
			'media' => function($q) { $q->orderBy('pivot_order', 'asc'); }
		])->findOrFail($id);
		return view('books.display', compact('book'));
	}

	/** Displays new book creation page. */
	public function create() {
		$media = Medium::all();
		return view('books/create', compact('media'));
	}

	/** Create a new book in the database and links it with provided media. */
	public function store(Request $request) {
		$data = $request->validate($this->validation);
		$mediaIDs = array(); // Array containing all media ids to attach to the new book

		// Storing all uploaded images
		if(array_key_exists('files', $data)) {
			foreach($data['files'] as $file) {
				// Pushing new files ids for attachment
				array_push($mediaIDs, self::storeMedia($file));
			}
		}

		// Merging files uploaded and files from library together for attachment
		if(array_key_exists('media', $data)) {
			$mediaIDs = array_merge($data['media'], $mediaIDs);
		}

		// Saving book in database
		$book = auth()->user()->books()->create($data);

		/** Creating a new array with ids as key and a table of order_field => order as value.
		 * This table allows us to save data in the order field of the pivot table
		 * It is order following the original $mediaIDs array. Hence uploaded files will be ordered at the end.
		 */
		$mediaIDsWithOrder = [];
		foreach($mediaIDs as $order => $id) {
			$mediaIDsWithOrder[$id] = ['order' => $order+1];
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
	public function edit($id) {
		$media = Medium::all();
		$book = Book::with([
			'media' => function($q) { $q->orderBy('pivot_order', 'asc'); },
			'orders',
		])->findOrFail($id);
		return view('books/edit', compact('book', 'media'));
	}

	/** Updates the book's info and re-links media if necessary. */
	public function update(Book $book, Request $request) {
		$data = $request->validate($this->validation);
		$mediaIDs = array(); // Array containing all media ids to attach to the book

		// Storing all uploaded images
		if(array_key_exists('files', $data)) {
			foreach($data['files'] as $file) {
				// Pushing new files ids for attachment
				array_push($mediaIDs, self::storeMedia($file));
			}
		}

		// Merging uploaded and from library IDs to attach
		if(array_key_exists('media', $data)) {
			$mediaIDs = array_merge($data['media'], $mediaIDs);
		}

		/** Creating a new array with ids as key and a table of order_field => order as value.
		 * This table allows us to save data in the order field of the pivot table
		 * It is order following the original $mediaIDs array. Hence uploaded files will be ordered at the end.
		 */
		$mediaIDsWithOrder = [];
		foreach($mediaIDs as $order => $id) {
			$mediaIDsWithOrder[$id] = ['order' => $order+1];
		}

		/** We sync up the media array with the attach table.
		 *  If a media id is in mediaIDs, it is attached.
		 *  If it is not and was previously attached, it is detached.
		 */
		$book->media()->sync($mediaIDsWithOrder);

		if(empty($data['pre_order'])) {
			$data['pre_order'] = 0;
		}

		// Updating book
		$book->update($data);
		
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
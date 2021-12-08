<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookInfo;
use App\Models\Medium;
use App\Traits\MediaManager;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

/**
 * ----------------------------------------------- IMPORTANT NOTES -----------------------------------------------
 * 
 * This controller was originally build up around the Book model.
 * The Book model was representing one book and contained all its data (title, author, price, weight, etc).
 * 
 * Later we had to introduce a new feature : Book variations. This is a sub-item, a variation of the original book
 * that has the same author, but can have a different prices, different weights, and of course a different stock.
 * It's like the different sizes or colour of one piece of cloth for example.
 * 
 * Since the book model was the base model for the order system to calculate everything, we kept it and now it
 * represent a sub-item. We created a parent model, BookInfo, that holds the shared book's data, like author and
 * description. BookInfo model now represent the book, and it hasMany sub-items that still are represented by the
 * Book model (what we refer to "variations"). Yes, it is confusing, but it was the best approach not to have to
 * refractor all the code.
 * 
 * To resume that mess :
 * BookInfo : Parent book model with all the descriptive datas. hasMany Books (AKA variations).
 * Book : Variation model, the sub-item that contains its own media, price, weight & stock. belongsTo 1 BookInfo.
 * 
 * ---------------------------------------------------------------------------------------------------------------
*/

/**
 * Controller for the Books Library
 */
class BooksController extends Controller
{

	use MediaManager;
	use SoftDeletes;

	/** @var array $bookValidation Contains the validation rules for creating or updating a variation (Book model) */
	protected $bookValidation = [
		'label' => ['required', 'max:128'],
		'weight' => ['required', 'min:0', 'integer'],
		'stock' => ['required', 'integer'],
		'pre_order' => ['nullable', 'boolean'],
		'price' => ['required', 'min:0', 'numeric'],
		'media' => ['nullable', 'array'],
		'files' => ['required_without:media', 'array'],
		'files.*' => ['nullable', 'file', 'mimes:jpg,gif,png'],
	];

	/** @var array $infoValidation Contains the validation rules for creating or updating a BookInfo model */
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



  /**
   * Lists all books from the library for the frontend index
	 * Filters out books with no linked media
   *
   * @return \Illuminate\Http\Response
   */
  public function index() {

		$bookInfos = BookInfo::with('books.media')
		->orderBy('position', 'ASC')
		->get();

		// We need to filter out the books without linked media because gilde.js hangs if it has no child elements.
		// If books is empty after filter, we remove it from the collection because we don't want to display it on front page.
		$bookInfos->each(function($bookInfo, $key) use (&$bookInfos) {
			$bookInfo->books = $bookInfo->books->filter(function($book) {
				return $book->media->isNotEmpty();
			});
			if($bookInfo->books->isEmpty()) {
				$bookInfos->forget($key);
			}
		});
		
		return view('books/index', compact('bookInfos'));
	}



	/**
	 * Lists all books from the library
	 * (Index of the books library in backend)
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function list() {
		$bookInfos = BookInfo::orderBy('position', 'ASC')->get();

		// We need to filter out the books without linked media.
		// If all books are empty, that will trigger a 'No variaiton found' warning.
		$bookInfos->each(function($bookInfo, $key) use (&$bookInfos) {
			$bookInfo->books = $bookInfo->books->filter(function($book) {
				return $book->media->isNotEmpty();
			});
		});

		$archived = BookInfo::onlyTrashed()->count();
    return view('books/list', compact('bookInfos', 'archived'));
	}


	
	/**
	 * Displays the book resume in backend
	 *
	 * @param  \App\Models\BookInfo $bookInfo
	 * @return \Illuminate\Http\Response
	 */
	public function display(BookInfo $bookInfo) {
		return view('books.display', compact('bookInfo'));
	}



	/**
	 * Displays new book creation page
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		$media = Medium::all();
		return view('books/create', compact('media'));
	}



	/**
	 * Create a new book in the database and links it with provided media
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function store(Request $request) {
		$bookData = $request->validate($this->bookValidation);
		$infoData = $request->validate($this->infoValidation);
		$mediaIDs = array(); // Array containing all media ids to attach to the new book

		// Book's position
		$infoData['position'] = BookInfo::count();

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



	/**
	 * Displays the book edition page
	 *
	 * @param  \App\Models\BookInfo  $bookInfo
	 * @return \Illuminate\Http\Response
	 */
	public function edit(BookInfo $bookInfo) {

		// Eager loading all variations with trashed & media, and then partitioning it in 2 collection, trashed and regualr books
		[$bookInfo->trashedBooks, $bookInfo->books] = $bookInfo->books()->withTrashed()->with('media')->get()->partition(function($book) {
			return $book->trashed();
		});

		return view('books/edit', compact('bookInfo'));

	}



	/**
	 * Updates the book's info and re-links media if necessary
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \App\Models\BookInfo $bookInfo
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function update(Request $request, BookInfo $bookInfo) {

		$data = $request->validate($this->infoValidation);

		// Updating book
		$bookInfo->update($data);
		
		return redirect()->route('books')->with([
			'flash' => __('flash.book.updated'),
			'flash-type' => 'success'
		]);
	}



	/**
	 * Lists all archived books
	 * (Index of archives in backend)
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function archived() {
		$bookInfos = BookInfo::onlyTrashed()->get();
		$archived = $bookInfos->count();
		return view('books.archived', compact('bookInfos', 'archived'));
	}



	/**
	 * Archives a book (SoftDelete)	
	 *
	 * @param  \App\Models\BookInfo $bookInfo
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function archive(BookInfo $bookInfo) {
		$bookInfo->delete();
		// SoftDelete variations
		$bookInfo->books()->each(function($book) {
			$book->delete();
		});
		return redirect()->route('books')->with([
			'flash' => __('flash.book.archived'),
			'flash-type' => 'info'
		]);
	}



	/**
	 * Restores a book from archives to library
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function restore(int $id) {

		// Select 'last' position row
		$lastBookInfo = BookInfo::orderBy('position', 'DESC')->first();

		// Can't bind a deleted model, will throw a 404
		$restoredBookInfo = BookInfo::onlyTrashed()->findOrFail($id);

		// Restoring variations
		$restoredBookInfo->books()->onlyTrashed()->get()->each(function($book) {
			$book->restore();
		});

		$restoredBookInfo->position = $lastBookInfo->position + 1;
		$restoredBookInfo->save();
		$restoredBookInfo->restore();

		return redirect()->route('books.archives')->with([
			'flash' => __('flash.book.restored'),
			'flash-type' => 'success'
		]);
	}



	/**
	 * Permanently deletes a book from archives unless it is still linked to an active order
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function delete(int $id) {
		// Can't bind a deleted model, will throw a 404
		$bookInfo = BookInfo::onlyTrashed()->findOrFail($id);

		// Check if any variations is still attached to an order
		// Here we're using a foreach because we want to return an \Illuminate\Http\Response.
		foreach($bookInfo->books()->withTrashed()->get() as $book) {
			if($book->orders->isNotEmpty()) {
				return redirect()->route('books.archives')->with([
					'flash' => __('flash.book.still-linked'),
					'flash-type' => 'error'
				]);
			}
		}

		// Delete books (AKA variations)
		// We running a second loop that goes over all the books no matter what
		// The previous loop interrupts and redirect if book is still in an order,
		// and the following code won't be run anyway
		// We also call ->withTrashed()->get() on the relationship to delete all variation
		// If we were to call directly the models, the softdeleted variations would stay in the db
		$bookInfo->books()->withTrashed()->get()->each(function($book) {
			$book->media()->detach();
			$book->forceDelete();
		});

		// Finally we delete once and for all BookInfo
		$bookInfo->forceDelete();

		return redirect()->route('books.archives')->with([
			'flash' => __('flash.book.deleted'),
			'flash-type' => 'success'
		]);

	}

	

	/**
	 * Deletes all archived books, except the ones which are still linked to an active order
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function deleteAll() {

		$bookInfos = BookInfo::onlyTrashed()->get();

		// We don't want to delete books that are still linked with an active order
		$booksForDeletion = $bookInfos->filter(function($bookInfo) {

			$deleteBook = true;

			$bookInfo->books()->onlyTrashed()->get()->each(function($book) use (&$deleteBook) {
				if($book->orders->isNotEmpty()) {
					$deleteBook = false;
					return $deleteBook; // breaks from each loop
				}
			});

			return $deleteBook;

		});

		$booksNotDeleted = $bookInfos->diff($booksForDeletion);

		$booksForDeletion->each(function($bookInfo) {

			$bookInfo->books()->withTrashed()->get()->each(function($book) {

				$book->media()->detach();
				$book->forceDelete();

			});

			$bookInfo->forceDelete();

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



	/**
	 * Reorders books
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function reorder(Request $request) {

		$books = BookInfo::all();

		$data = $request->validate([
			'order' => ['string', 'required']
		]);

		$data['order'] = json_decode($data['order'], true);

		$books->each(function ($item) use ($data) {
			$item->position = $data['order'][$item->id];
			$item->save();
		});

		return response()->noContent();
	}
}
<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookInfo;
use App\Models\Medium;
use Illuminate\Http\Request;
use App\Traits\MediaManager;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Controller for the book's variation
 * 
 * ------------------ IMPORTANT NOTE ------------------
 * 
 * A book is represented by the BookInfo model.
 * Book's variations are represented by the Book model.
 * 
 * For more information, checkout the notes in
 * app/Http/Controllers/BooksController.php
 * 
 * ----------------------------------------------------
 */
class VariationsController extends Controller
{

	use MediaManager;
	use SoftDeletes;

	/** @var array $validation contains the validation rules for creating or updating a variation (Book model) */
	protected $validation = [
		'label' => ['required', 'max:128'],
		'weight' => ['required', 'min:0', 'integer'],
		'stock' => ['required', 'integer'],
		'pre_order' => ['nullable', 'boolean'],
		'price' => ['required', 'min:0', 'numeric'],
		'media' => ['nullable', 'array'],
		'files' => ['required_without:media', 'array'],
		'files.*' => ['nullable', 'file', 'mimes:jpg,gif,png'],
		'extra' => ['nullable', 'string', 'max:128'],
	];



	/**
	 * Show the form for creating a new resource
	 *
	 * @param \App\Models\BookInfo $bookInfo
	 * @return \Illuminate\Http\Response
	 */
	public function create(BookInfo $bookInfo)
	{
		$media = Medium::all();
		return view('books.variations.create', compact('bookInfo', 'media'));
	}



	/**
	 * Store a newly created resource in storage
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \App\Models\BookInfo $bookInfo
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function store(Request $request, BookInfo $bookInfo)
	{

		$data = $request->validate($this->validation);

		// Variation's position
		$data['position'] = $bookInfo->books()->count();

		// New variation (or new book)
		$book = $bookInfo->books()->create($data);

		// Media	
		$mediaIDs = array(); // Array containing all media ids to attach to the new book
		// We have to create an intermediate array  in case no $data['media'] is empty

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

		return redirect()->route('books.edit', $bookInfo->id);

	}



	/**
	 * Show the form for editing the specified resource
	 *
	 * @param \App\Models\Book $book
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Book $book)
	{
		$media = Medium::all();
		return view('books.variations.edit', compact('book', 'media'));
	}



	/**
	 * Update the specified resource in storage
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \App\Models\Book $book
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function update(Request $request, Book $book)
	{
		$data = $request->validate($this->validation);

		// Media	
		$mediaIDs = array(); // Array containing all media ids to attach to the new book
		// We have to create an intermediate array  in case no $data['media'] is empty

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
			$book->media()->sync($mediaIDsWithOrder);
		}

		// Assigning pre_order value if it's not present in data (if it's not selected)
		if(!isset($data['pre_order'])) {
			$data['pre_order'] = 0;
		}

		// Update
		$book->update($data);

		return redirect()->route('books.edit', $book->bookInfo->id);

	}



	/**
	 * Soft delete the specified resource
	 *
	 * @param \App\Models\Book $book
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function delete(Book $book) {

		if($book->orders->isEmpty()) {
			$book->forceDelete();
			return redirect()->back()->with([
				'flash' => __('flash.variations.deleted'),
				'flash-type' => 'success'
			]);
		} else {
			$book->delete();
			return redirect()->back()->with([
				'flash' => __('flash.variations.linked-warning'),
				'flash-type' => 'warning'
			]);
		}
	}



	/**
	 * Restores variation
	 *
	 * @param int $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function restore(int $id) {

		$variation = Book::withTrashed()->findOrFail($id);
		$variation->restore();

		return redirect()->back()->with([
			'flash' => __('flash.variations.restored'),
			'flash-type' => 'success'
		]);

	}



	/**
	 * Refresh soft deleted variation
	 *
	 * @param int $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function refresh(int $id) {

		$variation = Book::withTrashed()->findOrFail($id);
		return $this->delete($variation);

	}


	
	/**
	 * Reorders variations
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \App\Models\BookInfo $bookInfo
	 * @return \Illuminate\Http\Response
	 */
	public function reorder(Request $request, BookInfo $bookInfo) {

		$variations = $bookInfo->books;

		$data = $request->validate([
			'order' => ['string', 'required']
		]);

		$data['order'] = json_decode($data['order'], true);

		$variations->each(function ($item, $key) use ($data) {
			$item->position = $data['order'][$item->id];
			$item->save();
		});

		return response()->noContent();

	}

}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medium;
use App\Models\Book;
use App\Traits\MediaManager;
use App\Http\Helpers\ImageOptimizer;

/**
 * Controller for the Media Library.
*/
class MediaController extends Controller
{
	use MediaManager;

    public function __contruct() {
    }

	/** Lists all media from the library. Index of the media library in backend. */
    public function list(){
        $media = Medium::orderBy('id', 'DESC')->get();
        return view('media/list', compact('media'));
    }

	/** Displays a single image with more info and with the renaming form. */
    public function display(Medium $medium) {
        return view('media/display', compact('medium'));
    }

	/** Displays new media creation page. */
    public function create() {
        return view('media/create');
    }

	/** 
	 * Saves uploaded files.
	 * Renames files if a specific name has been provided, otherwise it will use the original file name.
	 * If multiple files are uploaded with a specific name, they will be batch-renamed with an _index suffix.
	 * */
    public function store() {
		// Fields validation
        $data = request()->validate([
            'name' => ['max:64'],
			'files' => ['required', 'array'],
            'files.*' => ['file', 'mimes:jpg,gif,png'],
        ]);

		if(count($data['files']) <= 1) {
			if($data['name']) {
				self::storeMedia($data['files'][0], [
					'name' => $data['name'],
				]);
			} else {
				self::storeMedia($data['files'][0]);
			}
		} else {
			if($data['name']) {
				foreach($data['files'] as $key => $file) {
					self::storeMedia($file, [
						'name' => $data['name'].'_'.$key
					]);
				}
			} else {
				foreach($data['files'] as $key => $file) {
					self::storeMedia($file);
				}
			}
		}

        return redirect()->route('media')->with([
			'flash' => __('flash.media.added'),
			'flash-type' => 'success'
		]);
    }

	/** Updates a single file, basically renaming it. */
	public function update(Medium $medium, Request $request) {
		$data = $request->validate([
			'name' => ['required', 'string', 'max:64'],
		]);
		$medium->update($data);

		return redirect()->route('media.display', $medium)->with([
			'flash' => __('flash.media.renamed'),
			'flash-type' => 'info'
		]);
	}

	/** Breaks link between a medium and its related book. */
	public function breakLink(Medium $medium, Book $book) {
		$medium->books()->detach($book);
		return redirect()->route('media.display', $medium->id);
	}

	/**  Permanently deletes media from the library, and deletes all its related stored files. */
	public function delete($id) {
		$medium = Medium::with('books')->findOrFail($id);
		foreach($medium->books as $book) {
			$medium->books()->detach($book);
		}

		ImageOptimizer::clean('uploads/'.$medium->filename, true);
		
		$medium->delete();
		return redirect()->route('media')->with([
			'flash' => __('flash.media.deleted'),
			'flash-type' => 'success'
		]);
	}
}

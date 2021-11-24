<?php

namespace App\Http\Helpers;

use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;

/**
 * ImageOptimizer
 * Helper for generating optimized copies of an image file.
 * Copies are read from config/imageoptimizer.php
 * $filePath parameters always starts from the Storage folder.
 * Parent directory in the path should correspond to the family key in config/imageoptimizer.php
 * Ex : family/filehash.ext
 */
class ImageOptimizer {

	/**
	 * Generates optimized copies of a stored media and rename them with their appropriate suffixes.
	 * @param string $filePath The original file from Storage. Check class doc for more info.
	 */
	public static function run(string $filePath) {

		// $fileInfo['dirname'] = imageoptimizer.family
		$fileInfo = pathinfo($filePath);
		
		// Loading Image
		$imgManager = new ImageManager(array('driver' => config('app.driver')));

		// TODO Check if family directory exists before
		// For each presets found in imageoptimizer.family
		foreach(config('imageoptimizer.'.$fileInfo['dirname']) as $preset => $config) {
			if (!Storage::disk('public')->exists($fileInfo['dirname'].'/'.$fileInfo['filename'].'_'.$preset.'.'.$fileInfo['extension'])) {
				// TODO Check for storage link with an exception
				$img = $imgManager
					->make('storage/'.$filePath) // ImageIntervention needs to load image direclty from its path in order to read EXIF data.
					->orientate() // Re-orient images from phones (Orientation info is in EXIF data).
					// Resizing image only if original is bigger than 
					->fit($config['width'], $config['height'], function ($constraint) use($config) {
						if(!$config['upsize']) {
							$constraint->upsize();
						}
					})
					->encode($fileInfo['extension'], $config['quality']);
				Storage::disk('public')->put($fileInfo['dirname'].'/'.$fileInfo['filename'].'_'.$preset.'.'.$fileInfo['extension'], (string) $img);
			}
		}
	}

	/** Deletes all optimized copies for a medium
	 * @param string $filePath The original file from Storage. Check class doc for more info.
	 * @param bool $original Weather or not we should delete the original file (Optional, default to false).
	 */
	public static function clean(string $filePath, $original = false) {

		$fileInfo = pathinfo($filePath);

		if($original) {
			Storage::disk('public')->delete($filePath);
		}

		foreach(config('imageoptimizer.'.$fileInfo['dirname']) as $preset => $config) {
			Storage::disk('public')->delete($fileInfo['dirname'].'/'.$fileInfo['filename'].'_'.$preset.'.'.$fileInfo['extension']);
		}
	}

	/** Run optimization for all media in family
	 * @param string $family Family name, corresponding to the family key in config/imageoptimizer.php and file's parent directory.
	 */
	public static function runAll(string $family) {
		$files = Storage::disk('public')->files($family);
		
		// Filtering out other files than original
		$originals = array_filter($files, function($item) use($family) {
			return preg_match('/^'.$family.'\/([A-Za-z0-9]{40})\.(jpg|gif|jpeg|png)$/', $item);
		});

		foreach($originals as $path) {
			self::run($path);
		}
	}

	/** Deletes all optimized copies for all media in family
	 * @param string $family Family name, corresponding to the family key in config/imageoptimizer.php and file's parent directory.
	 * @param bool $original Weather or not we should delete the original file (Optional, default to false).
	 */
	public static function cleanAll(string $family, $original = false) {
		$files = Storage::disk('public')->files($family);
		
		// Filtering out original files
		if(!$original) {
			$files = array_filter($files, function($item) use($family){
				return !preg_match('/^'.$family.'\/([A-Za-z0-9]{40})\.(jpg|gif|jpeg|png)$/', $item);
			});
		}

		foreach($files as $path) {
			Storage::disk('public')->delete($path);
		}

		return redirect(route('media'));
	}
}

?>
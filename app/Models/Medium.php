<?php

namespace App\Models;

use Exception;
use App\Exceptions\PresetNotFoundException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medium extends Model
{
	use HasFactory;

	// We are only using the created_at timestamp here
	const UPDATED_AT = null;

	protected $fillable = [
		'name',
		'filehash',
		'extension',
	];

	public function user() {
		return $this->belongsTo(User::class);
	}
	
	// Media relation with books
	public function books() {
		return $this->belongsToMany(Book::class, 'book_medium', 'medium_id', 'book_id');
	}

	public function getFilenameAttribute() {
		return $this->attributes['filehash'].'.'.$this->attributes['extension'];
	}

	public function preset(string $preset, string $family = 'uploads') {

		$filename = $this->attributes['filehash'].'_'.$preset.'.'.$this->attributes['extension'];
		if(array_key_exists($preset, config('imageoptimizer.'.$family))) {
			return $family.'/'.$filename;
		} else {
			throw new PresetNotFoundException($message = $preset.' does not exist in imageoptimizer.'.$family.' config');
		}
		
	}
}

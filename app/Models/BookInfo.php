<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookInfo extends Model
{
	use HasFactory;
	use SoftDeletes;

	public $timestamps = false;

	protected $fillable = [
		'title',
		'author',
		'width',
		'height',
		'cover',
		'pages',
		'weight',
		'copies',
		'year',
		'description',
		'position',
	];

	public function books() {
		return $this->hasMany(Book::class)->has('media')->orderBy('position', 'ASC');
	}

	public function user() {
		return $this->belongsTo(User::class);
	}

}

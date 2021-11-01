<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookInfo extends Model
{
	use HasFactory;

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
		return $this->hasMany(Book::class)->orderBy('position', 'ASC');
	}

	public function user() {
		return $this->belongsTo(User::class);
	}

}

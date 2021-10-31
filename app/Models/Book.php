<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
  use HasFactory;
	use SoftDeletes;

	public $timestamps = false;

	protected $fillable = [
		'label',
		'pre_order',
		'price',
		'stock',
		'weight',
  ];

	public function setCartQuantity($value) {
		$this->attributes['cartQuantity'] = intval($value);
	}

	// Books relation with media
	public function media() {
		return $this->belongsToMany(Medium::class, 'book_medium', 'book_id', 'medium_id')->withPivot('order')->orderBy('pivot_order', 'asc');
	}

	// Book relation with orders
	public function orders() {
		return $this->belongsToMany(Order::class);
	}

	// Book relation with its bookInfo
	public function bookInfo() {
		return $this->belongsTo(BookInfo::class);
	}

}

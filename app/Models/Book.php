<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory;
	use SoftDeletes;

	protected $fillable = [
        'title',
        'author',
        'width',
		'height',
		'cover',
		'pages',
		'weight',
		'copies',
		'quantity',
		'pre_order',
		'year',
		'price',
		'description',
    ];

	public function getIsAvailable() {
		
	}

	public function setCartQuantity($value)
    {
        $this->attributes['cartQuantity'] = intval($value);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

	// Books relation with media
	public function media() {
		return $this->belongsToMany(Medium::class, 'book_medium', 'book_id', 'medium_id')->withPivot('order');
	}

	// Book relation with orders
	public function orders() {
		return $this->belongsToMany(Order::class);
	}

}

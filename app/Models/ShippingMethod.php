<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingMethod extends Model
{
  use HasFactory;
	use SoftDeletes;

	public $timestamps = false;

	protected $fillable = [
		'label',
		'price',
		'max_weight',
		'rule',
		'info',
	];

	public function orders() {
		return $this->hasMany(Order::class);
	}

	public function priceStops() {
		return $this->hasMany(PriceStop::class)->orderBy('price', 'ASC');
	}
}

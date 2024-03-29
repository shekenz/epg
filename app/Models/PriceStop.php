<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceStop extends Model
{
    use HasFactory;

	public $timestamps = false;

	protected $fillable = [
		'price',
		'weight',
		'shipping_method_id',
	];

	public function shippingMethods() {
		return $this->belongsTo(ShippingMethod::class);
	}
}

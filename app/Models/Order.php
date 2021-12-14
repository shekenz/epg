<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

	protected $appends = ['created_at_fdate', 'created_at_ftime'];

	protected $fillable = [
		'order_id',
		'transaction_id',
		'payer_id',
		'surname',
		'given_name',
		'full_name',
		'phone_number',
		'contact_email',
		'email_address',
		'address_line_1',
		'address_line_2',
		'admin_area_2',
		'admin_area_1',
		'postal_code',
		'country_code',
		'coupon_id',
		'shipping_method_id',
		'total_weight',
		'shipped_at',
		'tracking_url',
		'status',
		'pre_order',
		'read',
		'hidden',
	];

	public function getCreatedAtFDateAttribute() {
		return ucfirst($this->created_at->locale(config('app.locale'))->isoFormat('LL'));
	}

	public function getCreatedAtFTimeAttribute() {
		return ucfirst($this->created_at->locale(config('app.locale'))->isoFormat('LT'));
	}

	public function books() {
		return $this->belongsToMany(Book::class)->withTrashed()->withPivot('quantity');
	}

	public function shippingMethods() {
		return $this->belongsTo(ShippingMethod::class, 'shipping_method_id')->withTrashed();
	}

	public function coupons() {
		return $this->belongsTo(Coupon::class, 'coupon_id')->withTrashed();
	}

	public function priceStops() {
		return $this->hasManyThrough(PriceStop::class, ShippingMethod::class);
	}
}

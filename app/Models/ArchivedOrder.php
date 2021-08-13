<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchivedOrder extends Model
{
    use HasFactory;

	public $timestamps = false;

	protected $fillable = [
		'id',
		'order_id',
		'transaction_id',
		'payer_id',
		'surname',
		'given_name',
		'full_name',
		'phone_number',
		'email_address',
		'address_line_1',
		'address_line_2',
		'admin_area_2',
		'admin_area_1',
		'postal_code',
		'country_code',
		'books_data',
		'coupon_data',
		'shipping_data',
		'total_weight',
		'shipped_at',
		'tracking_url',
		'status',
		'pre_order',
		'created_at',
	];
}

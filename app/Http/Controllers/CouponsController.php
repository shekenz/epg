<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\Order;
use Carbon\Carbon;

class CouponsController extends Controller
{

	public $validation = [
		'label' => [
			'unique:coupons,label',
			'max:8',
			'required',
		],
		'value' => [
			'numeric',
			'required',
		],
		'type' => [
			'boolean',
			'required',
		],
		'quantity' => [
			'numeric',
		],
		'starts_at' => [
			'date',
			'required',
		],
		'expires_at' => [
			'date',
			'nullable',
			'after:starts_at',
		]
	];

    public function add(Request $request) {
		$data = $request->validate($this->validation);
		Coupon::create($data);

		return back();
	}
	
	/**
	 * get
	 *
	 * @param  Request $request
	 * @param  str $couponLabel
	 */
	public function get(Request $request, string $couponLabel) {
		if($request->wantsJson()) {
			$coupon = Coupon::where('label', $couponLabel)->first();
			if($coupon && (empty($coupon->expires_at) || (!empty($coupon->expires_at) && $coupon->expires_at->gt(\Carbon\Carbon::now()))) && (($coupon->used < $coupon->quantity && $coupon->quantity > 0) || ($coupon->quantity === 0))) {
				return response()->json($coupon);
			} else {
				return response()->json();
			}
		} else {
			return abort(404);
		}
	}

	public function delete(Coupon $coupon) {
		$order = Order::where('coupon_id', $coupon->id)->first();

		// Soft delete if shipping method is still linked to an order.
		if($order) {
			$coupon->delete();
		} else {
			$coupon->forceDelete();
		}

		return back();
	}
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShippingMethod;
use App\Traits\ShopControls;
use App\Models\Order;

class ShippingMethodsController extends Controller
{

	use ShopControls;

	protected $validation = [
		"label" => ['required', 'string', 'unique:App\Models\ShippingMethod,label'],
		"price" => ['required', 'numeric'],
		"tracking_url" => ['string', 'nullable'],
	];

    public function add(Request $request) {
		$data = $request->validate($this->validation);
		ShippingMethod::create($data);
		return redirect()->route('settings');
	}
	
	public function delete(ShippingMethod $shippingMethod) {
		$order = Order::where('shipping_method_id', $shippingMethod->id)->first();

		// Soft delete if shipping method is still linked to an order.
		if($order) {
			$shippingMethod->delete();
		} else {
			$shippingMethod->forceDelete();
		}

		if($this->isShopNotAvailable()) {
			$this->shopOff();
		}

		return redirect()->route('settings');
	}
}

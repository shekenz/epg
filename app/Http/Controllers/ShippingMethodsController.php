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
		'label' => ['required', 'string', 'unique:App\Models\ShippingMethod,label'],
		'price' => ['required', 'numeric'],
		'max_weight' => ['required', 'numeric'],
		'rule' => ['nullable'],
		'info' => ['nullable'],
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
			// TODO needs to delete also atatched shippingStops
		}

		if($this->isShopNotAvailable()) {
			$this->shopOff();
		}

		return redirect()->route('settings');
	}

	public function edit(ShippingMethod $shippingMethod) {
		return view('shipping-methods.edit',compact('shippingMethod'));
	}

	public function update(Request $request, ShippingMethod $shippingMethod) {

		$shippingMethod->load('priceStops');

		$this->validation['label'][2] .= ','.$shippingMethod->id;

		if($shippingMethod->priceStops->isNotEmpty()) {
			array_push($this->validation['price'], 'max:'.($shippingMethod->priceStops->first()->price - 0.01));
		}

		$data = $request->validate($this->validation);

		$shippingMethod->update($data);
		return redirect()->route('settings');
	}
}

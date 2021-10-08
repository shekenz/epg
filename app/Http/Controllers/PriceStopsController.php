<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShippingMethod;
use App\Models\PriceStop;
use Illuminate\Validation\ValidationException;

class PriceStopsController extends Controller
{  

	protected $validation = [
		'weight' => ['required', 'integer', 'min:1'],
		'price' => ['required', 'numeric', 'min:0.01'],
	];

	public function add(Request $request, ShippingMethod $shippingMethod) {

		$data = $request->validate($this->validation);

		$shippingMethod->load('priceStops');
		if($shippingMethod->priceStops->isNotEmpty()) {
			// Finding neighbor stops
			$previousStop = $shippingMethod->priceStops->where('weight', '<', $data['weight'])->last();
			$nextStop = $shippingMethod->priceStops->where('weight', '>', $data['weight'])->first();

			// Checking for price stops integrity
			if($data['price'] <= $previousStop->price || ($nextStop !== null && $data['price'] >= $nextStop->price)) {
				throw ValidationException::withMessages(['price' => 'This value violates stop-points integrity']);
			}
		}

		// If priceStop already exists, we update it instead of creating a new one
		if($shippingMethod->priceStops->contains('weight', $data['weight'])) {
			$existingStop = PriceStop::where('weight', $data['weight'])->first();
			$existingStop->price = $data['price'];
			$existingStop->save();
		} else {
			PriceStop::create(array_merge([
				'shipping_method_id' => $shippingMethod->id
			], $data));
		}
		
		return redirect()->route('settings');
	}

	public function delete(PriceStop $priceStop) {
		
		$priceStop->delete();
		return redirect()->route('settings');

	}
}

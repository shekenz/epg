<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Http\Resources\OrderCollection;

class FilterOrderController extends Controller
{

	/** Array containing global conditions for the query (where clauses) */
	public $globalConditions = [];
		


	/**
	 * filter()
	 * Returns a filtered list of orders
	 * Applyed filter is a JSON object
	 *
	 * @param  Illuminate\Http\Request $request
	 * @return Illuminate\Http\Response
	 */
	public function filter(Request $request)
	{
		if(json_decode($request->getContent()) !== null)
		{

			$request->input('method', 'all');

			$data = $request->validate([
				'method' => 'nullable',
				'from' => 'nullable|date',
				'to' => 'nullable|date',
				'read' => 'nullable|boolean',
				'preorder' => 'nullable|boolean',
				'data' => 'nullable|string',
			]);

			// Defining global filters (From, To, Read, Preorders)
			if(isset($data['from'])) { array_push($this->globalConditions, ['created_at', '>=', $data['from']]); }
			if(isset($data['to'])) { array_push($this->globalConditions, ['created_at', '<=', $data['to']]); }

			if(isset($data['read'])) { array_push($this->globalConditions, ['read', (bool) $data['read'] ]); }
			if(isset($data['preorder'])) { array_push($this->globalConditions, ['pre_order', (bool) $data['preorder'] ]); }

			switch($data['method'])
			{
				case null :
				case 'all' : return new OrderCollection($this->all()); break;
				//case 'order' : return $this->like($data, 'order_id'); break;
				//case 'name' : return $this->like($data, 'full_name'); break;
				//case 'email' : return $this->like($data, 'email_address'); break;
				//case 'status' : return $this->exact($data, 'status'); break;
				//case 'book' : return $this->book(intval($data)); break;
				//case 'coupon' : return $this->exact($data, 'coupon_id', true); break;
				//case 'shipping' : return $this->exact($data, 'shipping_method_id'); break;
				default : return response()->noContent()->setStatusCode(422, 'Unknown method');
			}
		}
		else
		{
			return response()->noContent()->setStatusCode(422, 'Excpecting valid JSON object');
		}
	}


	
	/**
	 * Internal method that returns all orders
	 *
	 * @return \Illuminate\Support\Collection
	 */
	protected function all() {
		return Order::where($this->globalConditions)->orderBy('created_at', 'DESC')->get();
	}
}

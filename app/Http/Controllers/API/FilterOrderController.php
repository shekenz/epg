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
	 * Returns a filtered list of orders
	 * Filter is a JSON object
	 *
	 * @param  Illuminate\Http\Request $request
	 * @return Illuminate\Http\Response
	 */
	public function filter(Request $request)
	{
		if(json_decode($request->getContent()) !== null)
		{

			$request->input('method', 'all');

			$filter = $request->validate([
				'method' => 'nullable',
				'from' => 'nullable|date',
				'to' => 'nullable|date',
				'read' => 'nullable|boolean',
				'preorder' => 'nullable|boolean',
				'data' => 'nullable|string',
			]);

			// Defining global filters (From, To, Read, Preorders)
			if(isset($filter['from'])) { array_push($this->globalConditions, ['created_at', '>=', $filter['from']]); }
			if(isset($filter['to'])) { array_push($this->globalConditions, ['created_at', '<=', $filter['to']]); }

			if(isset($filter['read'])) { array_push($this->globalConditions, ['read', (bool) $filter['read'] ]); }
			if(isset($filter['preorder'])) { array_push($this->globalConditions, ['pre_order', (bool) $filter['preorder'] ]); }

			switch($filter['method'])
			{
				case null :
				case 'all' : return new OrderCollection($this->all()); break;
				case 'order' : return new OrderCollection($this->like($filter['data'], 'order_id')); break;
				case 'name' : return new OrderCollection($this->like($filter['data'], 'full_name')); break;
				case 'email' : return new OrderCollection($this->like($filter['data'], 'contact_email')); break;
				case 'status' : return new OrderCollection($this->exact($filter['data'], 'status')); break;
				case 'book' : return new OrderCollection($this->book(intval($filter['data']))); break;
				case 'coupon' : return new OrderCollection($this->exact($filter['data'], 'coupon_id', true)); break;
				case 'shipping' : return new OrderCollection($this->exact($filter['data'], 'shipping_method_id')); break;
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



	/**
	 * Internal method that returns orders containing a key-word in a certain column
	 * 
	 * @param mixed $data The key-word (can be null)
	 * @param string $column The column to search
	 * @return \Illuminate\Support\Collection
	 */
	protected function like($data, string $column) {
		if($data) {
			return Order::where(array_merge($this->globalConditions, [[$column, 'like', '%'.$data.'%']]))->orderBy('created_at', 'DESC')->get();
		} else {
			return $this->all();
		}
	}



	/**
	 * Internal method that returns orders with the exact key-word in a certain column
	 * 
	 * @param mixed $data The key-word (can be null)
	 * @param string $column The column to search
	 * @param bool $filterEmpty
	 * @return \Illuminate\Support\Collection
	 */
	protected function exact($data, string $column, bool $filterEmpty = false) {
		if($data || $filterEmpty) {
			return Order::where(array_merge($this->globalConditions, [[$column, $data]]))->orderBy('created_at', 'DESC')->get();
		} else {
			return $this->all();
		}
	}

	

	/**
	 * Internal method that returns orders linked to a variation, looked up by its exact label
	 * 
	 * @param mixed $data The variation label (can be null)
	 * @return \Illuminate\Support\Collection
	 */
	protected function book($data) {
		if($data) {
			// return Order::with(['books' => function($query) use ($data) {
			// 	$query->where('books.id', $data);
			// }])->where($this->globalConditions)->orderBy('created_at', 'DESC')->get();
			return Order::whereHas('books', function($q) use ($data) {
				$q->where('books.id', $data);
			})->where($this->globalConditions)->orderBy('created_at', 'DESC')->get();
		} else {
			return $this->all();
		}
	}
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Carbon;
use PDF;

class OrdersMassController extends Controller
{

	public $globalConditions = [];
	
	/** @var array $validation Validation rules that checks if we received an array of IDs */
	public $validation = [
		'ids' => ['nullable', 'array'],
		'ids.*' => ['numeric']
	];


	
	/**
	 * Generates a CSV file with all orders
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function csv(Request $request) {

		$data = $request->validate($this->validation);
		
		if(!empty($data)) {
			$orders = Order::find($data['ids']);

			$fileName = 'orders_'.Carbon::now()->toDateString().'.csv';

			$headers = array(
				'Content-type'        => 'text/csv',
				'Content-Disposition' => 'attachment; filename='.$fileName,
				'Pragma'              => 'no-cache',
				'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
				'Expires'             => '0'
			);

			$columns = ['id','name', 'email', 'address_1', 'address_2', 'city', 'region', 'postcode', 'country', 'delivery', 'tracking'];

			$callback = function() use($orders, $columns) {
				$file = fopen('php://output', 'w');
				fputcsv($file, $columns, ';');
				foreach ($orders as $order) {
					fputcsv(
						$file,
						[
							$order->order_id,
							$order->full_name,
							$order->email_address,
							$order->address_line_1,
							$order->address_line_2,
							$order->admin_area_2,
							$order->admin_area_1,
							$order->postal_code,
							$order->country_code,
							$order->shipping_method,
							$order->tracking_url
						],
						';'
					);
				}

				fclose($file);
			};

			return response()->stream($callback, 200, $headers);

		} else {
			return back();
		}
	}


	
	/**
	 * Hides all selected orders
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function hide(Request $request) {
		$data = $request->validate($this->validation);
			if(!empty($data)) {
			$orders = Order::find($data['ids']);
			$orders->each(function($order) {
				$order->hidden = true;
				$order->save();
			});
		}

		return back();
	}



	/**
	 * Unhides all selected orders
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function unhide(Request $request) {
		$data = $request->validate($this->validation);
		if(!empty($data)) {
			$orders = Order::find($data['ids']);

			$orders->each(function($order) {
				$order->hidden = false;
				$order->save();
			});
		}

		return back();
	}



		
	/**
	 * Search for specific order records depending on the provided conditions
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param 'all'|'order'|'name'|'email'|'status'|'book'|'coupon'|'shipping' $method The field by wich we filter the search result
	 * @param string $from The stringified date representing the starting date 
	 * @param string $end The stringified date representing the ending date
	 * @param string $visibility A stringified boolean to return or not hidden orders
	 * @param string $preorder A stringified boolean to return only pre-ordered orders
	 * @param optional $data The key-word to search the orders with
	 * @return void
	 */
	public function get(Request $request, string $method, string $from, string $end, string $visibility, string $preorder, $data = null) {
		if($request->wantsJson()) {
			$this->globalConditions = [
				['created_at', '>=', $from],
				['created_at', '<=', Carbon::create($end)->addDay()],
				['hidden', ($visibility !== 'false') ? true : false],
			];

			if($preorder !== 'false') {
				array_push($this->globalConditions, ['orders.pre_order', true]);
			}

			switch($method) {
				case 'all' : return $this->all(); break;
				case 'order' : return $this->like($data, 'order_id'); break;
				case 'name' : return $this->like($data, 'full_name'); break;
				case 'email' : return $this->like($data, 'email_address'); break;
				case 'status' : return $this->exact($data, 'status'); break;
				case 'book' : return $this->book(intval($data)); break;
				case 'coupon' : return $this->exact($data, 'coupon_id', true); break;
				case 'shipping' : return $this->exact($data, 'shipping_method_id'); break;
				default : return response()->json()->setStatusCode(400, '"'.$method.'" method not supported');
			}
		} else {
			return abort(404);
		}
	}

	public function getJSON(Request $request) {
		if($request->wantsJson()) {

			$request->input('method', 'all');

			$data = $request->validate([
				'method' => 'nullable',
				'from' => 'nullable|date',
				'to' => 'nullable|date',
				'hidden' => 'nullable|boolean',
				'preorder' => 'nullable|boolean',
				'data' => 'nullable|string',
			]);

			if(isset($data['from'])) { array_push($this->globalConditions, ['created_at', '>=', $data['from']]); }
			if(isset($data['to'])) { array_push($this->globalConditions, ['created_at', '<=', $data['to']]); }

			array_push($this->globalConditions, ['hidden', boolval($data['hidden']) ]);

			switch($data['method']) {
				case null : return $this->all(); break;
				case 'all' : return $this->all(); break;
				case 'order' : return $this->like($data, 'order_id'); break;
				case 'name' : return $this->like($data, 'full_name'); break;
				case 'email' : return $this->like($data, 'email_address'); break;
				case 'status' : return $this->exact($data, 'status'); break;
				case 'book' : return $this->book(intval($data)); break;
				case 'coupon' : return $this->exact($data, 'coupon_id', true); break;
				case 'shipping' : return $this->exact($data, 'shipping_method_id'); break;
				default : return response()->json()->setStatusCode(422, 'Unknown method');
			}
		}
	}


	
	/**
	 * Internal method that returns all orders
	 *
	 * @return \Illuminate\Support\Collection
	 */
	protected function all() {
		return Order::with('books')->where($this->globalConditions)->orderBy('created_at', 'DESC')->get();
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
			return Order::with('books')->where(array_merge($this->globalConditions, [[$column, 'like', '%'.$data.'%']]))->orderBy('created_at', 'DESC')->get();
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
			return Order::with('books')->where(array_merge($this->globalConditions, [[$column, $data]]))->orderBy('created_at', 'DESC')->get();
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
			return Order::with(['books' => function($query) use ($data) {
				$query->where('books.id', $data);
			}])->where($this->globalConditions)->orderBy('created_at', 'DESC')->get();
		} else {
			return $this->all();
		}
	}


	
	/**
	 * Generates a PDF
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param string $view Name of the PDF view to generate
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function pdf(Request $request, string $view) {
		$data = $request->validate($this->validation);
		if(!empty($data)) {
			$orders = Order::with(['books', 'coupons', 'shippingMethods'])->orderBy('created_at', 'DESC')->find($data['ids']);
			//$orders = Order::factory()->count(16)->make();
			$pdf = PDF::loadView('pdf.'.$view, compact('orders'));
			return $pdf->download($view.'_'.Carbon::now()->toDateString().'.pdf');
		} else {
			return back();
		}
	}
	


	/**
	 * Labels preview and configuration view
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
	 */
	public function labelsPreview(Request $request) {
		$data = $request->validate($this->validation);
		if(!empty($data)) {
			$orders = Order::with(['books', 'coupons'])->orderBy('created_at', 'DESC')->find($data['ids']);
			return view('pdf.labelsPreview', compact('orders'));
		}  else {
			return back();
		}
	}


	
	/**
	 * Generates the labels PDF
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param int $extra Quantity of empty label to insert before
	 * @return \Illuminate\Http\Response
	 */
	public function labels(Request $request, int $extra) {
		$extra = ($extra % 12);
		$data = $request->validate($this->validation);
			$orders = Order::with(['books', 'coupons'])->orderBy('created_at', 'DESC')->find($data['ids']);
			$pdf = PDF::loadView('pdf.labels', compact('orders', 'extra'));
			return $pdf->download('labels_'.Carbon::now()->toDateString().'.pdf');
	}
}

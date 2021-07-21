<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;
use PDF;

class OrdersMassController extends Controller
{
	public $globalConditions;

	public $validation = [
		'ids' => ['nullable', 'array'],
		'ids.*' => ['numeric']
	];

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

	public function unhide(Request $request) {
		$data = $request->validate($this->validation);
		if(!empty($data)) {
			$orders = Order::find($data['ids']);

			$orders->each(function($order) {
				$order->hidden = false;
				$order->save();
			});
		}

		return redirect()->route('orders');
	}

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
				case 'book' : return $this->book($data); break;
				case 'coupon' : return $this->exact($data, 'coupon_id'); break;
				case 'shipping' : return $this->exact($data, 'shipping_method_id'); break;
				default : return response()->json()->setStatusCode(400, '"'.$method.'" method not supported');
			}
		} else {
			return abort(404);
		}
	}

	protected function all() {
		return Order::with('books')->where($this->globalConditions)->orderBy('created_at', 'DESC')->get();
	}

	protected function like($data, string $column) {
		if($data) {
			return Order::with('books')->where(array_merge($this->globalConditions, [[$column, 'like', '%'.$data.'%']]))->orderBy('created_at', 'DESC')->get();
		} else {
			return $this->all();
		}
	}

	protected function exact($data, string $column) {
		if($data) {
			return Order::with('books')->where(array_merge($this->globalConditions, [[$column, $data]]))->orderBy('created_at', 'DESC')->get();
		} else {
			return $this->all();
		}
	}

	protected function book($data) {
		if($data) {
			return Order::with(['books' => function($query) use ($data) {
				$query->where('title', 'like', '%'.$data.'%');
			}])->where($this->globalConditions)->orderBy('created_at', 'DESC')->get();
		} else {
			return $this->all();
		}
	}

	public function pdf(Request $request, string $view) {
		$data = $request->validate($this->validation);
		if(!empty($data)) {
			$orders = Order::with(['books', 'coupons'])->orderBy('created_at', 'DESC')->find($data['ids']);
			//$orders = Order::factory()->count(16)->make();
			$pdf = PDF::loadView('pdf.'.$view, compact('orders'));
			return $pdf->download($view.'_'.Carbon::now()->toDateString().'.pdf');
		} else {
			return back();
		}
	}

	public function labelsPreview(Request $request) {
		$data = $request->validate($this->validation);
		if(!empty($data)) {
			$orders = Order::with(['books', 'coupons'])->orderBy('created_at', 'DESC')->find($data['ids']);
			//$orders = Order::factory()->count(16)->make();
			return view('pdf.labelsPreview', compact('orders'));
		}  else {
			return back();
		}
	}

	public function labels(Request $request, int $extra) {
		$extra = ($extra % 12);
		$data = $request->validate($this->validation);
			$orders = Order::with(['books', 'coupons'])->orderBy('created_at', 'DESC')->find($data['ids']);
			//$orders = Order::factory()->count(16)->make();
			$pdf = PDF::loadView('pdf.labels', compact('orders', 'extra'));
			return $pdf->download('labels_'.Carbon::now()->toDateString().'.pdf');
	}
}

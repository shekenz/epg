<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Carbon;
use PDF;
use Illuminate\Support\Facades\Cache;

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

			$columns = ['order', 'transaction' ,'name', 'email', 'address_1', 'address_2', 'city', 'region', 'postcode', 'country', 'delivery', 'tracking', 'date'];

			$callback = function() use($orders, $columns) {
				$file = fopen('php://output', 'w');
				fputcsv($file, $columns, ';');
				foreach ($orders as $order) {
					fputcsv(
						$file,
						[
							$order->order_id,
							$order->transaction_id,
							$order->full_name,
							$order->email_address,
							$order->address_line_1,
							$order->address_line_2,
							$order->admin_area_2,
							$order->admin_area_1,
							$order->postal_code,
							$order->country_code,
							$order->shippingMethods->label,
							$order->tracking_url,
							$order->created_at->locale(config('app.locale'))->isoFormat('L'),
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
	 * Set all selected orders as read
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function setReadState(Request $request, bool $read = false) {
		$data = $request->validate($this->validation);
			if(!empty($data)) {
			$orders = Order::find($data['ids']);
			$orders->each(function($order) use (&$read) {
				if($read && !$order->read) { Cache::decrement('newOrders'); }
				if(!$read && $order->read) { Cache::increment('newOrders'); }
				$order->read = $read;
				$order->save();
			});
		}

		return back();
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

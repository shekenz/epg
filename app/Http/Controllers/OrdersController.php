<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Book;
use App\Models\User;
use App\Models\ShippingMethod;
use App\Models\Coupon;
use App\Models\Client;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\SystemError;
use App\Mail\OrderConfirmation;
use App\Mail\OrderShipped;
use App\Mail\NewOrder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class OrdersController extends Controller
{
	protected $credentials;
	protected $provider;
	private $startTime;

	private function _elapsed() {
		return round((microtime(true) - $this->startTime) * 1000, 2);
	}

	protected function speedTest() {
		if(isset($this->startTime)) {
			$elapsed = $this->_elapsed();
			if($elapsed > 1000) {
				Log::warning('Script took more than 1 second to execute (Took '.$elapsed.' ms)');
			}
		}
	}

	public function __construct() {

		$this->startTime = microtime(true);

		$this->credentials = [
			'mode'    => (setting('app.paypal.sandbox')) ? 'sandbox' : 'live',
			'sandbox' => [
				'client_id'         => setting('app.paypal.client-id'),
				'client_secret'     => setting('app.paypal.secret'),
				'app_id'            => '',
			],
			'live' => [
				'client_id'         => setting('app.paypal.client-id'),
				'client_secret'     => setting('app.paypal.secret'),
				'app_id'            => '',
			],
			'payment_action' => 'Sale',
			'currency'       => 'EUR',
			'notify_url'     => '',
			'locale'         => '',
			'validate_ssl'   => true,
		];
		$this->provider = new PayPalClient;
		$this->provider->setApiCredentials($this->credentials);
		$this->provider->getAccessToken();
	}

	/**
	 * List all visible & active orders
	 *
	 * @return void
	 */
	public function list() {
		if(!Cache::has('newOrders'))
		{
			$this->refreshNewOrders();
		}
		$orders = Order::orderBy('created_at', 'DESC')->get();
		$coupons = Coupon::withTrashed()->get();
		$shippingMethods = ShippingMethod::withTrashed()->orderBy('price', 'ASC')->get();
		$books = Book::withTrashed()->orderBy('book_info_id', 'ASC')->get();
		return view('orders.list-vue', compact('orders', 'coupons', 'shippingMethods', 'books'));
	}
	
	/**
	 * display
	 *
	 * @param  mixed $id
	 * @return void
	 */
	public function display($id) {
		$order = Order::with(['books', 'coupons', 'shippingMethods', 'books.bookInfo'])->where('id', $id)->firstOrFail();

		// Decrementing newOrders cache
		if(!$order->read) {
			Cache::decrement('newOrders');
			$order->read = 1;
			$order->save();
		}

		return view('orders.display', compact('order'));
	}
	
	/**
	 * refreshNewOrders
	 *
	 * @return void
	 */
	public function refreshNewOrders() {
		$count = Order::where('read', 0)->count();
		Cache::put('newOrders', $count);
		return redirect()->route('orders');
	}
		
	/**
	 * createOrder
	 *
	 * @param  mixed $request
	 * @param  mixed $shippingCost
	 * @return void
	 */
	public function createOrder(Request $request, ShippingMethod $shippingMethod, int $couponID) {

		$data = $request->validate([
			'given_name' => 'required|string|max:140',
			'surname' => 'required|string|max:140',
			'phone_number' => 'nullable|string|max:15',
			'contact_email' => 'email|required',
			'address_line_1' => 'required|string|max:300',
			'address_line_2' => 'nullable|string|max:300',
			'admin_area_2' => 'required|string|max:120',
			'admin_area_1' => 'nullable|string|max:300',
			'postal_code' => 'required|string|max:60',
			'country_code' => 'required|string|max:2',
			'newsletter' => 'nullable|boolean',
		]);

		if(isset($data['newsletter'])) {
			$existing_client = Client::where('email', $data['contact_email'])->first();
			if($existing_client === NULL) {
				Client::create([
					'firstname' => $data['given_name'],
					'lastname' => $data['surname'],
					'email' => $data['contact_email'],
					'country_code' => $data['country_code'],
				]);
				Log::info('Created client '.$data['contact_email']);
			} else {
				$existing_client->firstname = $data['given_name'];
				$existing_client->lastname = $data['surname'];
				$existing_client->country_code = $data['country_code'];
				$existing_client->save();
				Log::info('Updated client '.$data['contact_email']);
			}
		}
		
		$shippingMethod->load('priceStops');

		$preOrder = false;
		$totalWeight = 0;

		if(!$request->session()->has('cart')) {
			Log::channel('paypal')->notice('Cart not found');
			return response()->json()->setStatusCode(404, 'Cart not found');
		}

		// CART
		$cart = $request->session()->get('cart', false);
		$booksInCart = Book::findMany(array_keys($cart));

		// ITEMS
		$totalItems = round(array_reduce($cart, function($total, $item) {
			return $total + ($item['price'] * $item['quantity']);
		}), 2);

		$items = [];
		if($booksInCart) {
			$booksInCart->each(function($book) use ($cart, &$items, &$preOrder, &$totalWeight) {
				if($cart[$book->id]['quantity'] > 0) {
					array_push($items, [
						'name' => ($book->bookInfo->books->count() > 1) ? $book->bookInfo->title.' - '.$book->label : $book->bookInfo->title,
						'unit_amount' => [
							'currency_code' => 'EUR',
							'value' => $book->price,
						],
						'quantity' => $cart[$book->id]['quantity'],
					]);

					// Reducing totalWeight
					$totalWeight += $cart[$book->id]['quantity'] * $book->weight;
	
					// Checking for pre_order
					if($book->pre_order) {
						$preOrder = true;
					}
				}
			});
		}

		// If coupon id is found, calculate $couponPrice (aka discount)
		if($couponID > 0) {
			$coupon = Coupon::find($couponID);
			if(boolval($coupon->type)) {
				// Type is €
				$couponPrice = $coupon->value;
			} else {
				// Type is %
				$couponPrice = round($coupon->value / 100 * $totalItems,2);
			}
		} else {
			$couponPrice = 0;
		}

		// Calculate shipping method price from price range
		$shippingPrice = findStopPrice($totalWeight, $shippingMethod->price, $shippingMethod->priceStops);

		// Calculate total including shippingPrice and couponPrice
		$total = round( $totalItems + $shippingPrice - $couponPrice, 2);


		// ORDER
		$paypalOrder = $this->provider->createOrder([
			'intent' => 'CAPTURE',
			'application_context' => [
				'shipping_preference' => 'SET_PROVIDED_ADDRESS',
			],
			'payer' => [
				'email_address' => $data['contact_email'],
				/*
				'name' => [
					'given_name' => 'André',
					'surname' => 'Michel',
				],
				'phone' => [
					'phone_number' => [
						'national_number' => '0032487528702',
					],
				],
				'address' => [
					'address_line_1' => '23 Rue Blanche',
					'address_line_2' => '',
					'admin_area_2' => 'Truc-sur-Bousin',
					'admin_area_1' => 'Normandie',
					'postal_code' => '28564',
					'country_code' => 'FR',
				],
				*/
			],
			'purchase_units' => [
				0 => [
					'amount' => [
						'currency_code'=> 'EUR',
						'value' => $total,
						'breakdown' => [
							'item_total' => [ // Total added items
								'currency_code'=> 'EUR',
								'value' => $totalItems,
							],
							'shipping' => [
								'currency_code'=> 'EUR',
								'value' => $shippingPrice,
							],
							'discount' => [
								'currency_code'=> 'EUR',
								'value' => $couponPrice,
							],
						],
					],
					'shipping' => [
						'name' => [
							'full_name' => $data['surname'].' '.$data['given_name'],
						],
						'type' => 'SHIPPING',
						'address' => [
							'address_line_1' => $data['address_line_1'],
							'address_line_2' => $data['address_line_2'],
							'admin_area_2' => $data['admin_area_2'],
							'admin_area_1' => (isset($data['admin_area_1'])) ? $data['admin_area_1'] : '',
							'postal_code' => $data['postal_code'],
							'country_code' => $data['country_code'],
						],
					],
					'items' => $items
				]
			]
		]);

		try {
			$order = Order::create([
				'order_id' => $paypalOrder['id'],
				'status' => $paypalOrder['status'],
				'phone_number' => (isset($data['phone_number'])) ? $data['phone_number'] : '',
				'contact_email' => $data['contact_email'],
				'shipping_method_id' => $shippingMethod->id,
				'total_weight' => $totalWeight,
				'pre_order' => ($preOrder),
				'coupon_id' => ($couponID !== 0) ? $couponID : null,
			]);
		} catch(Exception $e) {
			$order = Order::create([
				'status' => 'FAILED',
				'phone_number' => (isset($data['phone_number'])) ? $data['phone_number'] : '',
				'contact_email' => $data['contact_email'],
				'shipping_method_id' => $shippingMethod->id,
				'total_weight' => $totalWeight,
				'pre_order' => ($preOrder),
				'coupon_id' => ($couponID !== 0) ? $couponID : null,
			]);
			
			$customMessage = 'Can\'t create order! The Esteban error!';
			$fullMessage = $customMessage."\n\t".
				'in file '.$e->getFile().' at line '.$e->getLine()."\n\t".
				'Called by : createOrder'."\n\t".
				'Message : '.$e->getMessage()."\n\t".
				'Data : --------------------------------'."\n".
				print_r($paypalOrder, true);

			// Loggin error
			Log::channel('paypal')->critical($fullMessage);

			// Sending error email to admins
			$admins = User::where('role', 'admin')->get();
			$admins->each(function($admin) use($customMessage, $e, $paypalOrder) {
				Mail::to($admin->email)->send(new SystemError($customMessage, $e, $paypalOrder));
			});

			$errorResponse = (config('env') == 'local') ? ['error' => [
				'type' => 'internal',
				'file' => $e->getFile(),
				'line' => $e->getLine(),
				'message' => $e->getMessage(),
				'custom-message' => $customMessage,
				'paypal-data' => $paypalOrder,
			]] : [];

		} finally {

			// Attaching books from cart to Order
			$booksInCart->each(function($book) use(&$order, $cart) {
				$order->books()->attach($book->id, ['quantity' => $cart[$book->id]['quantity']]);
			});

			// Updating books quantity
			$booksInCart->each(function($book) use ($cart) {
				$book->stock = $book->stock - $cart[$book->id]['quantity'];
				$book->save();
			});

			return (isset($errorResponse)) ? response()->json($errorResponse)->setStatusCode(500, 'Paypal order creation failed') : $paypalOrder;
		}
	}
	
	/**
	 * capture
	 *
	 * @param  mixed $request
	 * @param  mixed $orderID
	 * @return void
	 */
	public function capture(Request $request, $orderID) {
		$paypalOrder = $this->provider->capturePaymentOrder($orderID);
		try{
			if(!isset($paypalOrder['error'])) {
				// process order
				$order = Order::with(['coupons', 'shippingMethods'])->where('order_id', $paypalOrder['id'])->first();

				// Check those optional fields, log if empty
				if(!empty($paypalOrder['payer']['name']['surname'])) {
					$order->surname = $paypalOrder['payer']['name']['surname'];
				} else {
					Log::channel('paypal')->critical('Can\'t read property "surname" from Paypal data for orderID '.$paypalOrder['id']);
				}
				if(!empty($paypalOrder['payer']['name']['given_name'])) {
					$order->given_name = $paypalOrder['payer']['name']['given_name'];
				} else {
					Log::channel('paypal')->critical('Can\'t read property "given_name" from Paypal data for orderID '.$paypalOrder['id']);
				}
				if(!empty($paypalOrder['purchase_units'][0]['shipping']['name']['full_name'])) {
					$order->full_name = $paypalOrder['purchase_units'][0]['shipping']['name']['full_name'];
				} else {
					Log::channel('paypal')->critical('Can\'t read property "full_name" from Paypal data for orderID '.$paypalOrder['id']);
				}

				// Check if optional address fields exists in paypal data, log if empty
				$shippingAddressFields = [
					'address_line_1',
					'address_line_2',
					'admin_area_2',
					'admin_area_1', 
					'postal_code',
					'country_code'
				];
				foreach($shippingAddressFields as $columnName) {
					if(!empty($paypalOrder['purchase_units'][0]['shipping']['address'][$columnName])) {
						$order->{$columnName} = $paypalOrder['purchase_units'][0]['shipping']['address'][$columnName];
					} else {
						Log::channel('paypal')->notice('Can\'t read property "'.$columnName.'" from Paypal data for orderID '.$paypalOrder['id']);
					}
				}

				try {
					// Crutial data, trigger exception if not found	in paypal data
					$order->status = $paypalOrder['status'];			
					$order->payer_id = $paypalOrder['payer']['payer_id'];
					$order->email_address = $paypalOrder['payer']['email_address'];
					$order->transaction_id = $paypalOrder['purchase_units'][0]['payments']['captures'][0]['id'];

					// Updating coupons count
					if($order->coupons) {
						$order->coupons->used++;
						$order->coupons->save();
					}

					// Notify admins
					$admins = User::where('role', 'admin')->get();
					$admins->each(function($admin) {
						Mail::to($admin->email)->send(new NewOrder());
					});

					//Notify client
					if(config('app.env') === 'local') {
						// In test environement, sends the confirmation email to me
						Mail::to('aureltrotebas@icloud.com')->send(new OrderConfirmation($order));
					} else {
						Mail::to($order->email_address)->send(new OrderConfirmation($order));
					}

				} catch(Exception $e) { 

					$transactionId = (isset($paypalOrder['purchase_units'][0]['payments']['captures'][0]['id'])) ? $paypalOrder['purchase_units'][0]['payments']['captures'][0]['id'] : 'TransactionID not found.';
					$order->status = 'FAILED';
					$order->transaction_id = $transactionId;

					$customMessage = 'Paypal data doesn\'t match Order Model mendatory data';
					$fullMessage = $customMessage."\n\t".
						'in file '.$e->getFile().' on line '.$e->getLine()."\n\t".
						'Called by : onApprouve'."\n\t".
						'Message : '.$e->getMessage()."\n\t".
						'OrderID : '.$paypalOrder['id']."\n\t".
						'TransactionID : '.$transactionId;

					Log::channel('paypal')->critical($fullMessage);

					// Sending error email to admins
					$admins = User::where('role', 'admin')->get();
					$admins->each(function($admin) use($customMessage, $e, $paypalOrder) {
						Mail::to($admin->email)->send(new SystemError($customMessage, $e, $paypalOrder));
					});

					$errorResponse = (config('env') == 'local') ? ['error' => [
						'type' => 'internal',
						'file' => $e->getFile(),
						'line' => $e->getLine(),
						'message' => $e->getMessage(),
						'custom-message' => $customMessage,
						'paypal-data' => $paypalOrder,
					]] : [];
				} finally {
					// Saving order in database
					$order->save();

					// Incrementing newOrders cache
					if(Cache::has('newOrders')) {
						Cache::increment('newOrders');
					} else {
						$this->refreshNewOrders();
					}
				}
			} else {
				Throw new Exception($paypalOrder['error']['name']);
			}
		} catch(Exception $e) {
			$errorLog = 'Paypal order capture responded with an error : '.$e->getMessage()."\n".
			'Details :'."\n";
			foreach($paypalOrder['error']['details'] as $value) {
				$errorLog .= "\t".$value['issue'].' : '.$value['description'];
			}

			Log::channel('paypal')->critical($errorLog);
			$admins = User::where('role', 'admin')->get();
			$admins->each(function($admin) use($orderID, $e, $paypalOrder) {
				Mail::to($admin->email)->send(new SystemError('Paypal order '.$orderID.' capture failed', $e, $paypalOrder));
			});

			$errorResponse = $paypalOrder;

		} finally {
			// Emptying Cart
			$request->session()->forget('cart');
			
			return (isset($errorResponse)) ? response()->json($errorResponse)->setStatusCode(500, 'Paypal order processing failed') : $paypalOrder;
		}
	}
	
	/**
	 * checkCountry
	 * Check if country is listed for shippment
	 *
	 * @param  mixed $request
	 * @param  mixed $countryCode
	 * @return void
	 */
	public function checkCountry(Request $request, $countryCode) {
		return (in_array($countryCode, setting('app.shipping.allowed-countries')) || empty(setting('app.shipping.allowed-countries')))
			? [ 'country' => true ]
			: response()->json()->setStatusCode(500, 'Country code not accepted by the store.');
	}
	
	/**
	 * cancel
	 * Cancel an order
	 *
	 * @param  mixed $orderID
	 * @return void
	 */
	public function cancel(Request $request, Order $order) {

		// Getting order relationship
		$order->load('books');

		try {
			// Reinserting quantities in stock
			$order->books()->each(function($book) {
				$book->stock = $book->stock + $book->pivot->quantity;
				$book->save();
			});
			// Detaching books
			$order->books()->detach();
			// Deleting order
			$order->delete();

		} catch(Exception $e) {
			
			$customMessage = 'Can\'t delete order';
			$fullMessage = $customMessage."\n\t".
				'in file '.$e->getFile().' on line '.$e->getLine()."\n\t".
				'Message : '.$e->getMessage()."\n\t".
				'OrderID : '.$order->order_id;

			Log::channel('paypal')->critical($fullMessage);

			// Sending error email to admins
			$admins = User::where('role', 'admin')->get();
			$admins->each(function($admin) use($customMessage, $e, $order) {
				Mail::to($admin->email)->send(new SystemError($customMessage, $e, $order));
			});

			$errorResponse = true;

		} finally {
			if($request->wantsJson()) {
				return (isset($errorResponse)) ? response()->json()->setStatusCode(500, 'Can\'t delete order') : [ 'deleted' => $order->order_id ];
			} else {
				return (isset($errorResponse)) ? abort(500) : back();
			}
		}
		
	}
	
	/**
	 * Return details of an order.
	 *
	 * @param  string $orderID
	 */
	public function details($orderID) {
		return $this->provider->showOrderDetails($orderID);
	}
	
	
	/**
	 * recycle
	 *
	 * @param  strig $orderID
	 */
	public function recycle(string $orderID) {
		$details = $this->details($orderID);
		if(isset($details['error']) || (isset($details['status']) && $details['status'] === 'CREATED')) {
			$order = Order::where('order_id', $orderID)->first();
			$this->cancel(request(), $order);
			return redirect()->route('orders');
		} else {
			return redirect()->route('orders')->with([
				'flash' => __('flash.paypal.recycle'),
				'flash-type' => 'warning'
			]);;
		}	
	}
	
	/**
	 * Process an order as shipped.
	 *
	 * @param  mixed $orderID
	 * @return void
	 */
	public function shipped(Request $request, $id) {
		$order = Order::findOrFail($id);

		//TODO try catch maybe ?
		// Also
		if($order->status == 'COMPLETED')
		{
			$data = $request->validate([
				'tracking_url' => ['nullable', 'string'],
			]);
			$order->status = 'SHIPPED';
			$order->shipped_at = Carbon::now();
			$order->tracking_url = $data['tracking_url'];

			if(config('app.env') === 'local') {
				// In test environement, sends the confirmation email to me
				Mail::to('aureltrotebas@icloud.com')->send(new OrderShipped($order));
			} else {
				Mail::to($order->email_address)->send(new OrderShipped($order));
			}

			$order->save();
		} else {
			response()->setStatusCode(422, 'Status is not complete');
		}
		
		$this->speedTest();

		if($request->wantsJson()) {
			return response()->noContent();
		} else {
			return back();
		}
	}

}

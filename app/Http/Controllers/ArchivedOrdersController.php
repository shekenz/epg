<?php

namespace App\Http\Controllers;

use App\Models\ArchivedOrder;
use App\Models\Order;

class ArchivedOrdersController extends Controller
{
    	
	/**
	 * Archive an order (Version 2 : variation structure)
	 *
	 * @param  \App\Models\Order $order
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function archive(Order $order) {

		$order->load(['books', 'shippingMethods', 'coupons']);

		// Compacting books data
		$booksData = [];
		$order->books->each(function($book) use(&$booksData) {
			// Formating full title (BookInfo title + book label)
			$bookNewTitle = $book->bookInfo->title.' - '.$book->label;
			$bookAuthor = $book->bookInfo->author;
			// Stripping out not needed data
			$book->makeHidden(['weight', 'label', 'stock', 'year', 'deleted_at', 'position']);
			// Setting the order quantity in the book data array
			$book = $book->toArray();
			$book['quantity'] = $book['pivot']['quantity'];
			$book['title'] = $bookNewTitle;
			$book['author'] = $bookAuthor;
			// Cleaning pivot
			unset($book['pivot']);
			array_push($booksData, $book);
		});

		// Compacting coupon data
		$coupon = '';
		if(isset($order->coupons)) {
			$order->coupons->makeHidden(['id', 'quantity', 'used', 'created_at', 'starts_at', 'expires_at', 'deleted_at']);
			$coupon = $order->coupons;
		}

		// Compacting shipping method data
		$order->shippingMethods->makeHidden(['id', 'deleted_at']);
		$shippingMethod = $order->shippingMethods->toArray();
		$shippingMethod['price'] = findStopPrice($order->total_weight, $order->shippingMethods->price, $order->shippingMethods->priceStops);
		
		$archivedOrder = ArchivedOrder::create([
			'id' => $order->id,
			'order_id' => $order->order_id,
			'transaction_id' => $order->transaction_id,
			'payer_id' => $order->payer_id,
			'surname' => $order->surname,
			'given_name' => $order->given_name,
			'full_name' => $order->full_name,
			'phone_number' => $order->phone_number,
			'email_address' => $order->email_address,
			'address_line_1' => $order->address_line_1,
			'address_line_2' => $order->address_line_2,
			'admin_area_2' => $order->admin_area_2,
			'admin_area_1' => $order->admin_area_1,
			'postal_code' => $order->postal_code,
			'country_code' => $order->country_code,
			'books_data' => json_encode($booksData),
			'coupon_data' => json_encode($coupon),
			'shipping_data' => json_encode($shippingMethod),
			'total_weight' => $order->total_weight,
			'shipped_at' => $order->shipped_at,
			'tracking_url' => $order->tracking_url,
			'status' => $order->status,
			'pre_order' => $order->pre_order,
			'created_at' => $order->created_at,
			'version' => config('app.archives_version'),
		]);

		$order->books()->detach();
		$order->delete();

		return redirect()->back();
	}
	
	/**
	 * Display an archived order
	 *
	 * @param  \App\Models\ArchivedOrder $archivedOrder
	 * @return \Illuminate\Http\Response
	 */
	public function display(ArchivedOrder $archivedOrder) {
		return view('orders.archived.display', compact('archivedOrder'));
	}

	public function list() {
		$archivedOrders = ArchivedOrder::orderBy('archived_at', 'DESC')->get();

		return view('orders.archived.list', compact('archivedOrders'));
	}

}

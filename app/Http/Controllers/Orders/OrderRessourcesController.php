<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

use App\Models\Coupon;
use App\Models\ShippingMethod;
use App\Models\Book;

class OrderRessourcesController extends Controller
{
		/**
		 * Display a listing of the resource.
		 *
		 * @return \Illuminate\Http\Response
		 */
		public function index()
		{
			$coupons = Coupon::withTrashed()->get();
			$shippingMethods = ShippingMethod::withTrashed()->orderBy('price', 'ASC')->get();
			$books = Book::withTrashed()->orderBy('book_info_id', 'ASC')->get();
			return view('orders.list-vue', compact('coupons', 'shippingMethods', 'books'));
		}

		/**
		 * Show the form for creating a new resource.
		 *
		 * @return \Illuminate\Http\Response
		 */
		public function create()
		{
				//
		}

		/**
		 * Store a newly created resource in storage.
		 *
		 * @param  \Illuminate\Http\Request  $request
		 * @return \Illuminate\Http\Response
		 */
		public function store(Request $request)
		{
				//
		}

		/**
		 * Display the specified resource.
		 *
		 * @param  \App\Models\Order  $order
		 * @return \Illuminate\Http\Response
		 */
		public function show(Order $order)
		{
				//
		}

		/**
		 * Show the form for editing the specified resource.
		 *
		 * @param  \App\Models\Order  $order
		 * @return \Illuminate\Http\Response
		 */
		public function edit(Order $order)
		{
				//
		}

		/**
		 * Update the specified resource in storage.
		 *
		 * @param  \Illuminate\Http\Request  $request
		 * @param  \App\Models\Order  $order
		 * @return \Illuminate\Http\Response
		 */
		public function update(Request $request, Order $order)
		{
				//
		}

		/**
		 * Remove the specified resource from storage.
		 *
		 * @param  \App\Models\Order  $order
		 * @return \Illuminate\Http\Response
		 */
		public function destroy(Order $order)
		{
				//
		}
}

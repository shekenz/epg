<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderCollection;
use Illuminate\Support\Facades\Cache;

class OrderController extends Controller
{
		/**
		 * Display a listing of the resource.
		 *
		 * @return \Illuminate\Http\Response
		 */
		public function index()
		{
			return new OrderCollection(Order::with('books.bookInfo')->all());
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
			// Avoiding N+1 requests on bookInfo
			$order->load('books.bookInfo');
			if(!$order->read) {
				$order->read = true;
				$order->save();
				if(Cache::has('newOrders')) {
					Cache::decrement('newOrders');
				}
			}
			return new OrderResource($order);
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

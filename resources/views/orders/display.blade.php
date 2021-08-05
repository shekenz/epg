<x-app-layout>

	<x-slot name="title">
		{{ __('Order') }}
	</x-slot>

	<x-slot name="scripts">
		<script src="{{ asset('js/order-ship.js') }}" defer></script>
	</x-slot>


	@php
		$couponPrice = 0;

		switch ($order->status) {
			case 'FAILED':
				$statusClass = 'bg-red-500';
				break;
			case 'CREATED':
				$statusClass = 'bg-yellow-500';
				break;
			case 'COMPLETED':
				$statusClass = 'bg-blue-500';
				break;
			case 'SHIPPED':
				$statusClass = 'bg-green-500';
				break;
			default:
				$statusClass = 'bg-black';
				break;
		}
	@endphp

	<div>
		<div class="flex mt-6 justify-between items-center">
			<div class="">
				<span class="text-white text-xl py-4 px-6 {{ $statusClass }}">{{ mb_strtoupper(__('paypal.status.'.$order->status)) }}</span>
				@if($order->status == 'COMPLETED')
					<form id="ship-form" class="inline-block" action="{{ route('orders.shipped', $order->order_id) }}" method="POST">
						@csrf
						<button id="ship-button" class="inline-block align-middle"><x-tabler-truck-delivery class="text-gray-400 hover:text-green-500 mx-2 w-12 h-12 inline-block" /></button> 
					</form>
				@endif
				{{-- For debug ONLY ----- To be removed --}}
				@if($order->status == 'SHIPPED')
					<form class="inline-block" action="{{ route('orders.shipped', $order->order_id) }}" method="POST">
						@csrf
						<button id="ship-button" class="inline-block align-middle"><x-tabler-truck-return class="text-gray-400 hover:text-green-500 mx-2 w-12 h-12 inline-block" /></button> 
					</form>
				@endif
			</div>
			<div class="font-bold">
				<span class="mr-2">{{ __('Transaction ID') }} : </span><a href="@if(setting('app.paypal.sandbox')) {{ 'https://www.sandbox.paypal.com/activity/payment/'.$order->transaction_id  }}
				@else {{ 'https://www.paypal.com/activity/payment/'.$order->transaction_id  }}
				@endif" class="new-tab border border-black box-border text-xl py-4 px-6">{{ $order->transaction_id }}</a>
			</div>
		</div>


		<div class="flex gap-x-8 mt-8">
			<div class="flex-grow">
				<h2 class="text-lg border-b border-black font-bold">{{ __('Client info') }} : </h2>
				<div class="p-4">
					<p class="my-2"><span class="font-bold">{{ __('Ordered at') }} : </span>{{ $order->created_at }}</p>
					<p class="my-2"><span class="font-bold">{{ __('Order ID') }} : </span>{{ $order->order_id }}</p>
					<p class="my-2"><span class="font-bold">{{ __('Client ID') }} : </span>{{ $order->payer_id }}</p>
					<p class="my-2"><span class="font-bold">{{ __('Client') }} : </span>{{ $order->given_name }} {{ $order->surname }}</p>
					<p class="my-2"><span class="font-bold">{{ __('Client email') }} : </span><a href="mailto:{{ $order->email_address }}" class="hover:underline">{{ $order->email_address }}</a></p>
				</div>
			</div>
			<div class="flex-grow">
				<h2 class="text-lg border-b border-black font-bold">{{ __('Shipping address') }} : </h2>
				<div class="text-2xl font-bold border-4 border-black py-4 px-8 block w-96 mx-auto my-8">
					<p>{{ $order->full_name }}</p>
					<p>{{ $order->address_line_1 }}</p>
					<p>{{ $order->address_line_2 }}</p>
					<p>{{ $order->postal_code }} {{ $order->admin_area_2 }}, {{ $order->admin_area_1 }}</p>
					<p>@isset($order->country_code)
						{{ strtoupper(config('countries')[$order->country_code]) }}
					@endisset</p>
				</div>
				@if($order->status == 'SHIPPED')
				<h2 class="text-lg border-b border-black font-bold">{{ __('Shipping info') }} : </h2>
				<div class="p-4">
					<p class="my-2"><span class="font-bold">{{ __('Shipped at') }} : </span>{{ $order->shipped_at }}</p>
					<p class="my-2"><span class="font-bold">{{ __('Shipping method') }} : </span>{{ $order->shippingMethods->label }}</p>
					@isset($order->tracking_url)
					<p class="my-2"><span class="font-bold">{{ __('Tracking URL') }} : </span><a class="new-tab hover:underline" href="{{ $order->tracking_url }}">{{ $order->tracking_url }}</a></p>
					@endif
				</div>
			@endisset
			</div>
			
		</div>

		<div class="mt-6">
			<h2 class="text-lg border-b border-black font-bold">{{ __('Articles') }}</h2>
			<table class="w-full">
				<thead class="border-b-2 border-black">
					<td>{{ __('Title') }}</td>
					<td>{{ __('Author') }}</td>
					<td>{{ __('Quantity') }}</td>
					@if($order->pre_order)
						<td></td>
					@endif
					<td>{{ __('Subtotal') }}</td>
				</thead>
			@php $total = 0; @endphp
			@foreach ($order->books as $book)
				@php $total += round($book->pivot->quantity * $book->price, 2) @endphp
				<tr>
					<td>@if($book->trashed())<span title="{{ __('Book is archived') }}"><x-tabler-alert-triangle class="inline-block text-yellow-500" /></span>@endif {{ $book->title }}</td>
					<td>{{ $book->author }}</td>
					<td>{{ $book->pivot->quantity }}</td>
					@if($order->pre_order)
						@if($book->pre_order)
							<td><span class="font-bold text-sm inline-block text-white px-2 py-0.5 rounded bg-blue-500">{{ __('Pre-order') }}</span></td>
						@else
							<td></td>
						@endif
					@endif
					<td>{{ round($book->pivot->quantity * $book->price, 2) }} €</td>
				</tr>
			@endforeach
				@isset($order->coupons)
				@php
					if(boolval($order->coupons->type)) {
						$couponPrice =  $order->coupons->value * -1;
					}else{
						$couponPrice = round($order->coupons->value / -100 * $total, 2);
					}
				@endphp
				<tr class="border-b-2 border-t-2 border-black">
					<td>{{ __('Coupon') }}</td>
					<td>{{ $order->coupons->label }}</td>
					<td>-{{ $order->coupons->value }}@if(boolval($order->coupons->type)){{ '€' }}@else{{ '%' }}@endif</td>
					@if($order->pre_order)
						<td></td>
					@endif
					<td>{{ $couponPrice }}€</td>
				</tr>
				@endif
				<tr class="border-b-2 border-t-2 border-black">
					<td>{{ __('Shipping method') }}</td>
					<td>{{ $order->shippingMethods->label }}</td>
					<td></td>
					@if($order->pre_order)
						<td></td>
					@endif
					<td>{{ $order->shippingMethods->price }} €</td>
				</tr>
				<tfoot>
					<td>{{ __('Total') }}</td>
					<td></td>
					<td></td>
					@if($order->pre_order)
						<td></td>
					@endif
					<td class="font-bold">{{ round($order->shippingMethods->price + $total + $couponPrice, 2) }} €</td>
				</tfoot>
			</table>
		</div>

		<form action="{{ route('orders.print', 'packaging-list') }}" method="POST" class="my-8 flex justify-end">
			@csrf
			<input type="hidden" name="ids[]" value="{{ $order->id }}">
			<input type="submit" class="button-shared cursor-pointer" value="Download PDF">
		</form>
	</div>
		

</x-app-layout>
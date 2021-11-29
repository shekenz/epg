<x-app-layout>

	<x-slot name="title">
		{{ __('Archived Order') }}
	</x-slot>

	<x-slot name="scripts">
		<script src="{{ asset('js/order-ship.js') }}" defer></script>
	</x-slot>


	@php
		$couponPrice = 0;
		$books = json_decode($archivedOrder->books_data);
		$coupon = json_decode($archivedOrder->coupon_data);
		$shippingMethod = json_decode($archivedOrder->shipping_data);
		//dump($coupon);

		switch ($archivedOrder->status) {
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
		<div class="bg-yellow-500 text-white font-bold text-center p-4">{{ __('This order has been archived').' (Version '.$archivedOrder->version.')' }}</div>
		{{-- Header --}}
		<div class="flex mt-6 justify-between items-center">
			<div class="">
				<span class="text-white text-xl py-4 px-6 {{ $statusClass }}">{{ mb_strtoupper(__('paypal.status.'.$archivedOrder->status)) }}</span>
				@if($archivedOrder->status == 'COMPLETED')
					<form id="ship-form" class="inline-block" action="{{ route('orders.shipped', $archivedOrder->order_id) }}" method="POST">
						@csrf
						<button id="ship-button" class="inline-block align-middle"><x-tabler-truck-delivery class="text-gray-400 hover:text-green-500 mx-2 w-12 h-12 inline-block" /></button> 
					</form>
				@endif
			</div>
			<div class="font-bold">
				<span class="mr-2">{{ __('Transaction ID') }} : </span><a href="@if(setting('app.paypal.sandbox')) {{ 'https://www.sandbox.paypal.com/activity/payment/'.$archivedOrder->transaction_id  }}
				@else {{ 'https://www.paypal.com/activity/payment/'.$archivedOrder->transaction_id  }}
				@endif" class="new-tab border border-black box-border text-xl py-4 px-6">{{ $archivedOrder->transaction_id }}</a>
			</div>
		</div>


		<div class="flex gap-x-8 mt-8">

			{{-- Client info --}}
			<div class="flex-grow">
				<h2 class="text-lg border-b border-black font-bold">{{ __('Client info') }} : </h2>
				<div class="p-4">
					<p class="my-2"><span class="font-bold">{{ __('Ordered at') }} : </span>{{ $archivedOrder->created_at }}</p>
					<p class="my-2"><span class="font-bold">{{ __('Order ID') }} : </span>{{ $archivedOrder->order_id }}</p>
					<p class="my-2"><span class="font-bold">{{ __('Client ID') }} : </span>{{ $archivedOrder->payer_id }}</p>
					<p class="my-2"><span class="font-bold">{{ __('Client') }} : </span>{{ $archivedOrder->surname }} {{ $archivedOrder->given_name }}</p>
					<p class="my-2"><span class="font-bold">{{ __('Client email') }} : </span><a href="mailto:{{ $archivedOrder->email_address }}" class="hover:underline">{{ $archivedOrder->email_address }}</a></p>
					<p class="my-2"><span class="font-bold">{{ __('Client phone') }} : </span>{{ $archivedOrder->phone_number }}</p>
				</div>
			</div>

			{{-- Shipping address --}}
			<div class="flex-grow">
				<h2 class="text-lg border-b border-black font-bold">{{ __('Shipping address') }} : </h2>
				<div class="text-2xl font-bold border-4 border-black py-4 px-8 block w-96 mx-auto my-8">
					<p>{{ $archivedOrder->full_name }}</p>
					<p>{{ $archivedOrder->address_line_1 }}</p>
					<p>{{ $archivedOrder->address_line_2 }}</p>
					<p>{{ $archivedOrder->postal_code }} {{ $archivedOrder->admin_area_2 }}, {{ $archivedOrder->admin_area_1 }}</p>
					<p>@isset($archivedOrder->country_code)
						{{ strtoupper(config('countries')[$archivedOrder->country_code]) }}
					@endisset</p>
				</div>
				
				<h2 class="text-lg border-b border-black font-bold">{{ __('Shipping info') }} : </h2>
				<div class="p-4">
					<p class="my-2"><span class="font-bold">{{ __('Total weight') }} : </span>{{ $archivedOrder->total_weight }}g</p>
					@if($archivedOrder->status == 'SHIPPED')
						<p class="my-2"><span class="font-bold">{{ __('Shipped at') }} : </span>{{ $archivedOrder->shipped_at }}</p>
						<p class="my-2"><span class="font-bold">{{ __('Shipping method') }} : </span>{{ $shippingMethod->label }}</p>
						@isset($archivedOrder->tracking_url)
						<p class="my-2"><span class="font-bold">{{ __('Tracking URL') }} : </span><a class="new-tab hover:underline" href="{{ $archivedOrder->tracking_url }}">{{ $archivedOrder->tracking_url }}</a></p>
						@endisset
					@endif
				</div>
			</div>
			
		</div>

		{{-- Articles details --}}
		<div class="mt-6">
			<h2 class="text-lg border-b border-black font-bold">{{ __('Articles') }}</h2>
			<table class="w-full">
				<thead class="border-b-2 border-black">
					<td>{{ __('Title') }}</td>
					<td>{{ __('Author') }}</td>
					<td>{{ __('Quantity') }}</td>
					@if($archivedOrder->pre_order)
						<td></td>
					@endif
					<td>{{ __('Subtotal') }}</td>
				</thead>
			@php $total = 0; @endphp
			@foreach ($books as $book)
				@php 
					$total += round($book->quantity * $book->price, 2)
				@endphp
				<tr>
					<td>{{ $book->title }}</td>
					<td>{{ $book->author }}</td>
					<td>{{ $book->quantity }}</td>
					@if($archivedOrder->pre_order)
						@if(boolval($book->pre_order))
							<td><span class="font-bold text-sm inline-block text-white px-2 py-0.5 rounded bg-blue-500">{{ __('Pre-order') }}</span></td>
						@else
							<td></td>
						@endif
					@endif
					<td>{{ round($book->quantity * $book->price, 2) }} €</td>
				</tr>
			@endforeach
				@if(!empty($coupon))
					@php
						if(boolval($coupon->type)) {
							$couponPrice =  $coupon->value * -1;
						}else{
							$couponPrice = round($coupon->value / -100 * $total, 2);
						}
					@endphp
					<tr class="border-b-2 border-t-2 border-black">
						<td>{{ __('Coupon') }}</td>
						<td>{{ $coupon->label }}</td>
						<td>-{{ $coupon->value }}@if(boolval($coupon->type)){{ '€' }}@else{{ '%' }}@endif</td>
						@if($archivedOrder->pre_order)
							<td></td>
						@endif
						<td>{{ $couponPrice }}€</td>
					</tr>
				@endif
				<tr class="border-b-2 border-t-2 border-black">
					<td>{{ __('Shipping method') }}</td>
					<td>{{ $shippingMethod->label }}</td>
					<td></td>
					@if($archivedOrder->pre_order)
						<td></td>
					@endif
					<td>{{ $shippingMethod->price }} €</td>
				</tr>
				<tfoot>
					<td>{{ __('Total') }}</td>
					<td></td>
					<td></td>
					@if($archivedOrder->pre_order)
						<td></td>
					@endif
					<td class="font-bold">{{ round($shippingMethod->price + $total + $couponPrice, 2) }} €</td>
				</tfoot>
			</table>
		</div>

	</div>
		

</x-app-layout>
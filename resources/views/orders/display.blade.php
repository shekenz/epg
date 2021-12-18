<x-app-layout>

	<x-slot name="title">
		{{ ___('order') }}
	</x-slot>

	<x-slot name="scripts">
		<script src="{{ asset('js/order-ship.js') }}" defer></script>
	</x-slot>

	@php 
		//dd($order);
		$couponPrice = 0;
		$shippingPrice = 0;
		$total = 0;
	@endphp

	<x-section :return="route('orders')" :title="___('order').' '.$order->order_id" class="full">

		<x-buttons>
			<x-button disabled icon="truck-delivery" :label="___('dispatch') " href="#" class="big" />
			<div class="flex gap-x-4">
				<x-post :href="route('orders.labelsPreview')" :label="___('print label')" class="big" icon="printer">
					<input type="hidden" name="ids[]" value="{{ $order->id }}" />
				</x-post>
				<x-post :href="route('orders.print', 'packaging-list')" :label="___('packaging list')" class="big" icon="file-download">
					<input type="hidden" name="ids[]" value="{{ $order->id }}" />
				</x-post>
			</div>
		</x-buttons>

		<div class="flex gap-x-8 w-full">

			<div class="w-full">
				<table class="big">
					<thead>
						<tr>
							<td colspan="2">{{ ___('order informations') }}</td>
						<tr>
					</thead>
					<tbody>
						<tr>
							<td class="text-gray-600 dark:text-gray-400">{{ ___('status') }} :</td>
							<td><x-captions.order-status :status="$order->status"/></td>
						</tr>
						<tr>
							<td><span class="text-gray-600 dark:text-gray-400">{{ ___('transaction ID') }} :</span></td>
							@php $sandbox = setting('app.paypal.sandbox') ? 'sandbox.' : ''; @endphp
							<td><a class="text-inherit" target="_blank" href="{{ 'https://www.'.$sandbox.'paypal.com/activity/payment/'.$order->transaction_id }}">{{ $order->transaction_id }}</a></td>
						</tr>
						<tr>
							<td><span class="text-gray-600 dark:text-gray-400">{{ ___('order ID') }} :</span></td>
							<td>{{ $order->order_id }}</td>
						</tr>
						<tr>
							<td><span class="text-gray-600 dark:text-gray-400">{{ ___('ordered at') }} :</span></td>
							<td>{{ $order->created_at_fdate }}</td>
						</tr>
						<tr>
							<td><span class="text-gray-600 dark:text-gray-400">{{ ___('shipping method') }} :</span></td>
							<td>{{ $order->shippingMethods->label }}</td>
						</tr>
						<tr>
							<td><span class="text-gray-600 dark:text-gray-400">{{ ___('total weight') }} :</span></td>
							<td>{{ $order->total_weight.'g' }}</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div class="w-full">
				<table class="big">
					<thead>
						<tr>
							<td colspan="2">{{ ___('client data') }}</td>
						<tr>
					</thead>
					<tbody>
						<tr>
							<td><span class="text-gray-600 dark:text-gray-400">{{ ___('client ID') }} :</span></td>
							<td>{{ $order->payer_id }}</td>
						</tr>
						<tr>
							<td><span class="text-gray-600 dark:text-gray-400">{{ ___('client') }} :</span></td>
							<td>{{ $order->full_name }}</td>
						</tr>
						<tr>
							<td><span class="text-gray-600 dark:text-gray-400">{{ ___('contact email') }} :</span></td>
							<td><a class="text-inherit" href="mailto:{{ $order->contact_email }}">{{ $order->contact_email }}</a></td>
						</tr>
						<tr>
							<td><span class="text-gray-600 dark:text-gray-400">{{ ___('paypal address') }} :</span></td>
							<td>{{ $order->email_address }}</td>
						</tr>
						@if(!empty($order->phone_number))
						<tr>
							<td><span class="text-gray-600 dark:text-gray-400">{{ ___('phone number') }} :</span></td>
							<td>{{ $order->phone_number }}</td>
						</tr>
						@endif
					</tbody>
				</table>
			</div>

			<div class="w-full">
				<table class="big">			
					<thead>
						<tr>
							<td>{{ ___('shipping address') }}</td>
						<tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<ul class="py-4 px-12 text-lg">
									<li class="font-bold">{{ $order->full_name }}</li>
									<li>{{ $order->address_line_1 }}</li>
									<li>{{ $order->address_line_2 }}</li>
									<li>{{ $order->postal_code.' '.$order->admin_area_2 }}</li>
									<li>{{ $order->admin_area_1 }}</li>
									<li>{{ config('countries.'.$order->country_code) }}</li>
								</ul>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			
		</div>

		<table class="big mt-8">
			<thead>
				<tr>
					<td>{{ ___('title') }}</td>
					<td>{{ ___('variation') }}</td>
					<td>{{ ___('author') }}</td>
					<td>{{ ___('quantity') }}</td>
					<td>{{ ___('subtotal') }}</td>
				</tr>
			</thead>
			<tbody>
				@foreach($order->books as $book)
				@php 
					$shippingPrice = findStopPrice($order->total_weight, $order->shippingMethods->price, $order->shippingMethods->priceStops);
					$total += round($book->pivot->quantity * $book->price, 2)
				@endphp
				<tr>
					<td>{{ $book->bookInfo->title }}</td>
					<td>{{ $book->label }}</td>
					<td>{{ $book->bookInfo->author }}</td>
					<td>{{ $book->pivot->quantity }}</td>
					<td>{{ round($book->price * $book->pivot->quantity, 2) }} €</td>
				</tr>
				@endforeach

				@if($order->coupons)
				@php
					if(boolval($order->coupons->type)) {
						$couponPrice =  $order->coupons->value * -1;
					}else{
						$couponPrice = round($order->coupons->value / -100 * $total, 2);
					}
				@endphp
				<tr v-if="currentOrder.order.coupon" class="border-t border-primary">
					<td colspan="4">{{ ___('coupon') }} {{ $order->coupons->label }} ( -{{ $order->coupons->value.(($order->coupons->type) ? ' €' : '%') }} )</td>
					<td>{{ $couponPrice }} €</td>
				</tr>
				@endif
				<tr class="border-t border-primary">
					<td colspan="4">{{ ___('shipping method') }} : {{ $order->shippingMethods->label }}</td>
					<td>{{ $shippingPrice }} €</td>
				</tr>
				<tr class="border-t border-primary">
					<td colspan="4">{{ ___('total') }}</td>
					<td>{{ $total + $couponPrice + $shippingPrice }} €</td>
				</tr>
				
			</tbody>
		</table>

	</x-section>
		
</x-app-layout>
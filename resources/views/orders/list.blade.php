<x-app-layout>
	<x-slot name="title">
		@if(request()->routeIs('orders.hidden'))
			{{ __('Orders (hidden)') }}
		@else
			{{ __('Orders') }}
		@endif
	</x-slot>

	<x-slot name="scripts">
		<script src="{{ asset('js/order-list.js') }}" type="text/javascript" defer></script>
	</x-slot>

	@if(request()->routeIs('orders'))
	<x-slot name="controls">
		<a class="button-shared" href="{{ route('orders.hidden') }}">{{ __('Hidden orders') }}</a>
	</x-slot>
	@endif

	<div class="flex justify-between mb-3">
		<div>
			@php $maxDate = \Carbon\Carbon::now()->toDateString(); @endphp
			<label for="filter">{{ __('Filter') }}</label>
			<select class="input-inline" id="filter" placeholder="Filter">
				<option selected value="all">{{ __('None') }}</option>
				<option value="book">{{ __('Book') }}</option>
				<option value="order">{{ __('Order ID') }}</option>
				<option value="name">{{ __('Client name') }}</option>
				<option value="email">{{ __('Email') }}</option>
				<option value="status">{{ __('Status') }}</option>
				<option value="coupon">{{ __('Coupon') }}</option>
				<option value="shipping">{{ __('Shipping method') }}</option>
			</select>
			<label for="filter-data">{{ __('with') }}</label>
			<input class="input-inline" id="filter-data-text" type="text" disabled="true">
			<select class="input-inline hidden max-w-[16rem]" id="filter-data-book">
				<option value=""></option>
				@foreach ($books as $book)	
					<option value="{{ $book->id }}">{{ $book->title }}</option>
				@endforeach
			</select>
			<select class="input-inline hidden" id="filter-data-status">
				<option value="FAILED">{{ __('paypal.status.FAILED') }}</option>
				<option value="CREATED">{{ __('paypal.status.CREATED') }}</option>
				<option value="COMPLETED" selected="selected">{{ __('paypal.status.COMPLETED') }}</option>
				<option value="SHIPPED">{{ __('paypal.status.SHIPPED') }}</option>
			</select>
			<select class="input-inline hidden" id="filter-data-coupon">
				@foreach ($coupons as $coupon)
					<option value="{{ $coupon->id }}">{{ $coupon->label }}@if($coupon->trashed()) ({{ __('Trashed') }})@endif</option>
				@endforeach
			</select>
			<select class="input-inline hidden" id="filter-data-shipping">
				@foreach ($shippingMethods as $shippingMethod)
					<option value="{{ $shippingMethod->id }}">{{ $shippingMethod->label }}@if($shippingMethod->trashed()) ({{ __('Trashed') }})@endif</option>
				@endforeach
			</select>
			<label for="start-date">{{ __('from') }}</label>
			<input class="input-inline" id="start-date" type="date" value="{{ \Carbon\Carbon::now()->subYear(1)->toDateString()}}" max="{{ $maxDate }}">
			<label for="end-date">{{ __('to') }}</label>
			<input class="input-inline" id="end-date" type="date" value="{{ $maxDate }}" max="{{ $maxDate }}">
			<input class="ml-2" id="preorder" type="checkbox"><label for="preorder" class="label-shared"> {{ __('Pre-orders') }}</label>
			<img id="loader" class="hidden ml-2 w-6 h-6 inline-block" src="{{ asset('img/loader2.gif')}}">
			</select>
		</div>
	</div>
	<div class="flex items-end border-t pt-1">
		<x-tabler-corner-left-down class="ml-2 inline-block" />
		<div class="mb-2">
			@if(request()->routeIs('orders.hidden'))
			<input id="hide" class="button-small cursor-pointer action" type="button" data-action="{{ route('orders.unhide') }}" value="{{ __('Unhide') }}">
			@else
			<input id="hide" class="button-small cursor-pointer action" type="button" data-action="{{ route('orders.hide') }}" value="{{ __('Hide') }}">
			@endif
			<input id="csv" class="button-small cursor-pointer action" type="button" data-action="{{ route('orders.csv') }}" value="CSV">
			<input id="pdf" class="button-small cursor-pointer action" type="button" data-action="{{ route('orders.labelsPreview') }}" value="PDF">
		</div>
	</div>
	
	<form id="orders-selection" method="POST">
		@csrf
		<table id="orders-table" class="app-table">
			<thead>
				<td><input type="checkbox" id="checkall" title="{{ __('Select/Deselect all') }}"></td>
				<td>{{ __('Order') }}</td>
				<td>{{ __('Client') }}</td>
				<td>{{ __('Client email') }}</td>
				<td>{{ __('Pre') }}</td>
				<td>{{ __('Status') }}</td>
				<td>{{ __('Created at') }}</td>
				{{-- <td>{{ __('Last updated') }}</td> --}}
				<td>{{ __('Tools') }}</td>
			</thead>
			<tbody id="order-rows">
			@foreach ($orders as $order)
				<tr class="order-row @if(!$order->read){{ 'unread' }}@endif">
					<td><input class="checkbox" type="checkbox" value="{{$order->id }}" name="ids[]"</td>
					<td><a class="default" href="{{ route('orders.display', $order->id)}}">@if(isset($order->order_id))
						{{ $order->order_id }}
					@else
						{{ '[ '.__('Order ID missing').' ]'}}
					@endisset</a></td>
					<td>{{ $order->full_name }}</td>
					<td>{{ $order->email_address }}</td>
					<td class="text-center">@if($order->pre_order === 1)<x-tabler-clock />@endif</td>
					<td><span class="font-bold text-center inline-block w-full text-white px-2 py-1 rounded @switch($order->status)
						@case('FAILED') {{ 'bg-red-500' }} @break
						@case('CREATED') {{ 'bg-yellow-500' }} @break
						@case('COMPLETED') {{ 'bg-blue-500' }} @break
						@case('SHIPPED') {{ 'bg-green-500' }} @break
					@endswitch">
						{{ mb_strtoupper(__('paypal.status.'.$order->status)) }}
					</span></td>
					<td>{{ $order->created_at_formated }}</td>
					{{-- <td>{{ $order->updated_at }}</td> --}}
					<td class="text-right">
						@if($order->status == 'CREATED' && isset($order->order_id) && !$order->created_at->isCurrentHour())
							<a class="icon" href="{{ route('orders.recycle', $order->order_id) }}"><x-tabler-recycle /></a>
						@elseif($order->status == 'FAILED')
							<a class="icon" href="{{ route('orders.cancel', $order->id) }}"><x-tabler-trash /></a>
						@endif
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</form>

	<div id="no-result" class="hidden text-center text-xl text-gray-400 my-8">
		{{ __('No result found') }}
	</div>

	<x-tabler-recycle class="hidden" id="recycle-blueprint"/>
	<x-tabler-trash class="hidden" id="trash-blueprint"/>
	{{-- Used to be a forkilft icon, hence the id (used in order-list.js) --}}
	<x-tabler-clock class="hidden" id="forklift-blueprint"/>

</x-app-layout>
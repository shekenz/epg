<x-app-layout>
	<x-slot name="title">
			{{ __('Archived orders') }}
	</x-slot>

	<x-slot name="leftControls">
		<a href="{{ route('orders') }}" class="mini-button"><x-tabler-chevron-left /></a>
	</x-slot>

	<div class="flex justify-between mb-3">
		<div class="hidden">
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
				{{--
				@foreach ($books as $book)	
					<option value="{{ $book->id }}">{{ $book->title }}</option>
				@endforeach
				--}}
			</select>
			<select class="input-inline hidden" id="filter-data-status">
				<option value="FAILED">{{ __('paypal.status.FAILED') }}</option>
				<option value="CREATED">{{ __('paypal.status.CREATED') }}</option>
				<option value="COMPLETED" selected="selected">{{ __('paypal.status.COMPLETED') }}</option>
				<option value="SHIPPED">{{ __('paypal.status.SHIPPED') }}</option>
			</select>
			<select class="input-inline hidden" id="filter-data-coupon">
				<option value="">{{ __('None') }}</option>
				{{--
				@foreach ($coupons as $coupon)
					<option value="{{ $coupon->id }}">{{ $coupon->label }}@if($coupon->trashed()) ({{ __('Trashed') }})@endif</option>
				@endforeach
				--}}
			</select>
			<select class="input-inline hidden" id="filter-data-shipping">
				{{--
				@foreach ($shippingMethods as $shippingMethod)
					<option value="{{ $shippingMethod->id }}">{{ $shippingMethod->label }}@if($shippingMethod->trashed()) ({{ __('Trashed') }})@endif</option>
				@endforeach
				--}}
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
	<div class="flex items-end border-t pt-1 hidden">
		<x-tabler-corner-left-down class="ml-2 inline-block" />
		<div class="mb-2 hidden">
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
				<td class="hidden"><input type="checkbox" id="checkall" title="{{ __('Select/Deselect all') }}"></td>
				<td>{{ __('Order') }}</td>
				<td>{{ __('Client') }}</td>
				<td>{{ __('Client email') }}</td>
				<td>{{ __('Created at') }}</td>
				<td>{{ __('Archived at') }}</td>
				<td>{{ __('Version') }}</td>
			</thead>
			<tbody id="order-rows">
				@foreach($archivedOrders as $archiveOrder)
				<tr>
					<td class='hidden'>{{ $archiveOrder->id }}</td>
					<td><a class="underline text-gray-500 hover:text-inherit" href="{{ route('archive.display', $archiveOrder->id) }}">{{ $archiveOrder->order_id }}</a></td>
					<td>{{ $archiveOrder->full_name }}</td>
					<td>{{ $archiveOrder->email_address }}</td>
					<td>{{ $archiveOrder->created_at }}</td>
					<td>{{ $archiveOrder->archived_at }}</td>
					<td>{{ $archiveOrder->version }}</td>
				</tr>
				@endforeach
				{{--
				<tr>
					<td colspan="8"><img class="m-auto my-8" src="{{ asset('img/loader2.gif') }}" alt="loader animation"></td>
				</tr>
				--}}
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
	<x-tabler-truck-delivery class="hidden" id="shipped-blueprint"/>
	<x-tabler-archive class="hidden" id="archive-blueprint"/>

</x-app-layout>
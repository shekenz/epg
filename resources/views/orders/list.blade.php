<x-app-layout>
	<x-slot name="title">
		@if(request()->routeIs('orders.hidden'))
			{{ ___('orders (hidden)') }}
		@else
			{{ ___('orders') }}
		@endif
	</x-slot>

	<x-slot name="scripts">
		<script src="{{ asset('js/order-list.js') }}" type="text/javascript" defer></script>
	</x-slot>

	@if(request()->routeIs('orders'))
	<x-slot name="controls">
		<a class="button-shared" href="{{ route('orders.hidden') }}">{{ ___('hidden orders') }}</a>
		<a class="button-shared" href="{{ route('archive.list') }}">{{ ___('archived orders') }}</a>
	</x-slot>
	@endif

	<div class="flex justify-between mb-3">
		<div>
			@php $maxDate = \Carbon\Carbon::now()->toDateString(); @endphp
			<label for="filter">{{ ___('filter') }}</label>
			<select class="input-inline" id="filter" placeholder="Filter">
				<option selected value="all">{{ ___('none') }}</option>
				<option value="book">{{ ___('book') }}</option>
				<option value="order">{{ ___('order ID') }}</option>
				<option value="name">{{ ___('client name') }}</option>
				<option value="email">{{ ___('email') }}</option>
				<option value="status">{{ ___('status') }}</option>
				<option value="coupon">{{ ___('coupon') }}</option>
				<option value="shipping">{{ ___('shipping method') }}</option>
			</select>
			<label for="filter-data">{{ ___('with') }}</label>
			<input class="input-inline" id="filter-data-text" type="text" disabled="true">
			{{-- Books filter option --}}
			<select class="input-inline hidden max-w-[16rem]" id="filter-data-book">
				<option value=""></option>
				@foreach ($books as $book)	
					<option value="{{ $book->id }}">{{ $book->title }}@if($book->trashed()) ({{ ___('archived') }})@endif</option>
				@endforeach
			</select>
			{{-- Statut filter option --}}
			<select class="input-inline hidden" id="filter-data-status">
				<option value="FAILED">{{ __('paypal.status.FAILED') }}</option>
				<option value="CREATED">{{ __('paypal.status.CREATED') }}</option>
				<option value="COMPLETED" selected="selected">{{ __('paypal.status.COMPLETED') }}</option>
				<option value="SHIPPED">{{ __('paypal.status.SHIPPED') }}</option>
			</select>
			{{-- Coupon filter option --}}
			<select class="input-inline hidden" id="filter-data-coupon">
				<option value="">{{ ___('none') }}</option>
				@foreach ($coupons as $coupon)
					<option value="{{ $coupon->id }}">{{ $coupon->label }}@if($coupon->trashed()) ({{ ___('trashed') }})@endif</option>
				@endforeach
			</select>
			{{-- Shipping method filter option --}}
			<select class="input-inline hidden" id="filter-data-shipping">
				@foreach ($shippingMethods as $shippingMethod)
					<option value="{{ $shippingMethod->id }}">{{ $shippingMethod->label }}@if($shippingMethod->trashed()) ({{ ___('trashed') }})@endif</option>
				@endforeach
			</select>
			{{-- Datefilter option --}}
			<label for="start-date">{{ __('from') }}</label>
			<input class="input-inline" id="start-date" type="date" value="{{ \Carbon\Carbon::now()->subYear(1)->toDateString()}}" max="{{ $maxDate }}">
			<label for="end-date">{{ __('to') }}</label>
			<input class="input-inline" id="end-date" type="date" value="{{ $maxDate }}" max="{{ $maxDate }}">
			{{-- Pre-order filter option --}}
			<input class="ml-2" id="preorder" type="checkbox"><label for="preorder" class="label-shared"> {{ ___('pre-orders') }}</label>
			<img id="loader" class="hidden ml-2 w-6 h-6 inline-block" src="{{ asset('img/loader2.gif')}}">
			</select>
		</div>
	</div>
	<div class="flex items-end border-t pt-1">
		<x-tabler-corner-left-down class="ml-2 inline-block" />
		<div class="mb-2">
			@if(request()->routeIs('orders.hidden'))
			<input id="hide" class="button-small cursor-pointer action" type="button" data-action="{{ route('orders.unhide') }}" value="{{ ___('unhide') }}">
			@else
			<input id="hide" class="button-small cursor-pointer action" type="button" data-action="{{ route('orders.hide') }}" value="{{ ___('hide') }}">
			@endif
			<input id="csv" class="button-small cursor-pointer action" type="button" data-action="{{ route('orders.csv') }}" value="CSV">
			<input id="pdf" class="button-small cursor-pointer action" type="button" data-action="{{ route('orders.labelsPreview') }}" value="PDF">
		</div>
	</div>
	
	<form id="orders-selection" method="POST">
		@csrf
		<table id="orders-table" class="app-table">
			<thead>
				<td><input type="checkbox" id="checkall" title="{{ ___('select all') }}"></td>
				<td>{{ ___('order') }}</td>
				<td>{{ ___('client') }}</td>
				<td>{{ ___('email') }}</td>
				<td>{{ ___('pre') }}</td>
				<td>{{ ___('status') }}</td>
				<td>{{ ___('created at') }}</td>
				<td>{{ ___('tools') }}</td>
			</thead>
			<tbody id="order-rows">
				<tr>
					<td colspan="8"><img class="m-auto my-8" src="{{ asset('img/loader2.gif') }}" alt="loader animation"></td>
				</tr>
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
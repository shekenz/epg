<x-app-layout>
	<x-slot name="title">
		@if(request()->routeIs('orders.hidden'))
			{{ ___('hidden orders') }}
		@else
			{{ ___('orders') }}
		@endif
	</x-slot>

	@if(request()->routeIs('orders.hidden'))
	<x-slot name="leftControls">
		<a href="{{ route('orders') }}" class="mini-button"><x-tabler-chevron-left /></a>
	</x-slot>
	@endif

	<x-slot name="scripts">
		<script src="{{ asset('js/order-vue.js') }}" type="text/javascript" defer></script>
	</x-slot>

	@if(request()->routeIs('orders'))
	<x-slot name="controls">
		<a class="button-shared" href="{{ route('orders.hidden') }}">{{ ___('hidden orders') }}</a>
		<a class="button-shared" href="{{ route('archive.list') }}">{{ ___('archived orders') }}</a>
	</x-slot>
	@endif

	<x-section id="orders" :title="___('archived orders')" class="full">

		<form class="flex flex-col md:flex-row md:gap-x-8 md:items-center">

			{{-- Method input --}}
			<x-select :label="___('filter by')" type="text" name="method" v-model="filters.method" @change="filters.data = ''; getOrders()">
				<option v-for="method in methods" :value="method">@{{ $t('methods.'+method) }}</option>
			</x-select>

			{{-- Data (Keyword) input if method == [ all, order, name, email ] --}}
			<x-input ::disabled="filters.method === null || filters.method === 'all'" v-show="methodsTextDataType.indexOf(filters.method) >= 0 || filters.method === null" :label="___('keyword')" type="text" name="data" v-model="filters.data" @input="debounceInput"/>

			{{-- Data input if method == status --}}
			<x-select v-show="filters.method == 'status'" :label="___('status')" type="text" name="data" v-model="filters.data" @change="getOrders">
				<option value="FAILED">{{ __('paypal.status.FAILED') }}</option>
				<option value="CREATED">{{ __('paypal.status.CREATED') }}</option>
				<option value="COMPLETED" selected="selected">{{ __('paypal.status.COMPLETED') }}</option>
				<option value="SHIPPED">{{ __('paypal.status.SHIPPED') }}</option>
			</x-select>

			{{-- Data input if method == book --}}
			<x-select v-show="filters.method == 'book'" :label="___('book')" type="text" name="data" v-model="filters.data" @change="getOrders">
				<option value=""></option>
				@foreach ($books as $book)
					<option value="{{ $book->id }}">{{ $book->bookInfo['title'].' | '.$book->label }}@if($book->trashed()) ({{ ___('archived') }})@endif</option>
				@endforeach
			</x-select>

			{{-- Data input if method == coupon --}}
			<x-select v-show="filters.method == 'coupon'" :label="___('coupon')" type="text" name="data" v-model="filters.data" @change="getOrders">
				<option value="">{{ ___('none') }}</option>
				@foreach ($coupons as $coupon)
					<option value="{{ $coupon->id }}">{{ $coupon->label }}@if($coupon->trashed()) ({{ ___('trashed') }})@endif</option>
				@endforeach
			</x-select>

			{{-- Data input if method == shipping --}}
			<x-select v-show="filters.method == 'shipping'" :label="___('shipping')" type="text" name="data" v-model="filters.data" @change="getOrders">
				@foreach ($shippingMethods as $shippingMethod)
					<option value="{{ $shippingMethod->id }}">{{ $shippingMethod->label }}@if($shippingMethod->trashed()) ({{ ___('trashed') }})@endif</option>
				@endforeach
			</x-select>

			{{-- Global filters inputs --}}
			<x-input :label="___('start')" type="date" name="from" v-model="filters.from" @change="getOrders"/>
			<x-input :label="___('end')" type="date" name="to" v-model="filters.to" @change="getOrders"/>
			<x-select :label="___('Read / Unread')" name="read" v-model="filters.read" @change="getOrders">
				<option :value="null">{{ ___('all') }}</option>
				<option :value="false">{{ ___('unread') }}</option>
				<option :value="true">{{ ___('read') }}</option>
			</x-select>
			<x-select :label="___('orders')" name="preorder" v-model="filters.preorder" @change="getOrders">
				<option :value="null">{{ ___('all') }}</option>
				<option :value="false">{{ ___('regular orders') }}</option>
				<option :value="true">{{ ___('pre-orders') }}</option>
			</x-select>
		</form>

		<table class="big mt-4">
			<thead>
				<tr>
					<td>{{ ___('order') }}</td>
					<td>{{ ___('client') }}</td>
					<td>{{ ___('email') }}</td>
					<td>{{ ___('pre') }}</td>
					<td>{{ ___('status') }}</td>
					<td>{{ ___('created at') }}</td>
					<td>{{ ___('actions') }}</td>
			</thead>
			<tbody>
				<tr v-for="(order, key) in orders" :class="{ 'unread' : !order.read }">
					<td><a :href="route(order.id)">@{{ order.order_id }}</a></td>
					<td>@{{ order.name }}</td>
					<td>@{{ order.email }}</td>
					<td><x-tabler-clipboard-check v-if="order.pre_order" class="text-green-500"/></td>
					<td><x-captions.order-status /></td>
					<td>@{{ localDate(order.created_at) }}</td>
					<td>{{ ___('actions') }}</td>
				</tr>
			</tbody>
		</table>

	</x-section>

</x-app-layout>
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

	<div id="orders">
		<x-section :title="___('archived orders')" class="full" v-show="!currentOrder">

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
						<td>{{ ___('paypal.status.preorder') }}</td>
						<td>{{ ___('status') }}</td>
						<td>{{ ___('created at') }}</td>
						<td>{{ ___('actions') }}</td>
				</thead>
				<tbody>
					<tr v-for="(order, key) in orders" :class="{ 'unread' : !order.read }">
						<td><a :href="route(order.id)" @click.prevent="getOrder(order.id)">@{{ order.order_id }}</a></td>
						<td>@{{ order.name }}</td>
						<td>@{{ order.email }}</td>
						<td><x-tabler-clipboard-check v-if="order.pre_order" class="text-green-500"/></td>
						<td><x-captions.order-status status="order.status"/></td>
						<td>@{{ order.locale.created_date }}</td>
						<td>{{ ___('actions') }}</td>
					</tr>
				</tbody>
			</table>

		</x-section>

		<x-section return="#" @click.prevent="returnToList" ::title="'{{ ___('order') }} '+getCurrentOrder('order_id')" class="full" v-show="currentOrder">

			<div class="flex gap-x-8 w-full">

				<div class="w-full">
					<x-separator first>{{ ___('order informations') }}</x-separator>

					<div class="flex items-center gap-x-2 mb-2">
						<span class="text-gray-600 dark:text-gray-400">{{ ___('status') }} :</span>
						<div class="w-32 inline">
							<x-captions.order-status status="getCurrentOrder('order.status')"/>
						</div>
					</div>

					<ul>
						<li><span class="text-gray-600 dark:text-gray-400">{{ ___('transaction ID') }} :</span> <a class="text-inherit" target="_blank" :href="
							@if(setting('app.paypal.sandbox'))
							'https://www.sandbox.paypal.com/activity/payment/'+getCurrentOrder('order.transaction_id')
							@else
							'https://www.sandbox.paypal.com/activity/payment/'+getCurrentOrder('order.transaction_id')
							@endif
						">@{{ getCurrentOrder('order.transaction_id') }}</a>&nbsp;<x-tabler-brand-paypal class="inline w-5 h-5"/></li>
						<li><span class="text-gray-600 dark:text-gray-400">{{ ___('order ID') }} :</span> @{{ getCurrentOrder('order.id') }}</li>
						<li><span class="text-gray-600 dark:text-gray-400">{{ ___('ordered at') }} :</span> @{{ getCurrentOrder('meta.locale.created_date') }}</li>
						<li><span class="text-gray-600 dark:text-gray-400">{{ ___('shipping method') }} :</span> @{{ getCurrentOrder('shipping.method.label') }}</li>
						<li><span class="text-gray-600 dark:text-gray-400">{{ ___('total weight') }} :</span> @{{ getCurrentOrder('shipping.total_weight')+'g' }}</li>
					</ul>
				</div>

				<div class="w-full">
					<x-separator first>{{ ___('client data') }}</x-separator>
					<ul>
						<li><span class="text-gray-600 dark:text-gray-400">{{ ___('client ID') }} :</span> @{{ getCurrentOrder('payer.id') }}</li>
						<li><span class="text-gray-600 dark:text-gray-400">{{ ___('client') }} :</span> @{{ getCurrentOrder('payer.full_name') }}</li>
						<li><span class="text-gray-600 dark:text-gray-400">{{ ___('contact email') }} :</span> <a class="text-inherit" :href="'mailto:'+getCurrentOrder('payer.contact_email')">@{{ getCurrentOrder('payer.contact_email') }}</a></li>
						<li><span class="text-gray-600 dark:text-gray-400">{{ ___('paypal address') }} :</span> @{{ getCurrentOrder('payer.paypal_address') }}</li>
						<li v-show="getCurrentOrder('payer.phone_number')"><span class="text-gray-600 dark:text-gray-400">{{ ___('phone number') }} :</span> @{{ getCurrentOrder('payer.phone_number') }}</li>
					</ul>
				</div>
				
				<div class="w-full">
					<x-separator first>{{ ___('shipping address') }}</x-separator>
					<ul class="bg-white border-[3px] border-gray-800 text-gray-800 py-4 px-12 text-lg shadow-tight-window">
						<li class="font-bold">@{{ getCurrentOrder('payer.full_name') }}</li>
						<li>@{{ getCurrentOrder('shipping.address.line_1') }}</li>
						<li>@{{ getCurrentOrder('shipping.address.line_2') }}</li>
						<li>@{{ getCurrentOrder('shipping.address.postcode')+' '+getCurrentOrder('shipping.address.admin_area_2') }}</li>
						<li>@{{ getCurrentOrder('shipping.address.admin_area_1') }}</li>
						<li>@{{ getCurrentOrder('shipping.address.country') }}</li>
					</ul>
				</div>

			</div>

			<table class="big mt-8">
				<thead>
					<tr>
						<td>{{ ___('title') }}</td>
						<td>{{ ___('variation') }}</td>
						<td>{{ ___('author') }}</td>
						<td>{{ ___('quantity') }}</td>
						<td>{{ ___('sub-total') }}</td>
					</tr>
				</thead>
				<tbody>
					<tr v-for="book in getCurrentOrder('order.books')">
						<td>@{{ book.title }}</td>
						<td>@{{ book.variation }}</td>
						<td>@{{ book.author }}</td>
						<td>@{{ book.quantity }}</td>
						<td>@{{ book.total_price }}</td>
					</tr>
				</tbody>
			</table>

			<div class="mt-8">
				@{{currentOrder}}
			</div>
		</x-section>

	<div>

</x-app-layout>
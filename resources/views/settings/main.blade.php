<x-app-layout>
	<x-slot name="title">
		{{ ___('settings') }}
	</x-slot>

	<x-slot name="scripts">
		<script src="{{ asset('js/settings.js') }}" type="text/javascript" defer></script>
	</x-slot>

	{{-------------------------------------- Form Errors --------------------------------------}}
	@if ($errors->any())
		<div class="mb-4" :errors="$errors">
			<div class="font-medium text-red-600">
				{{ __('Whoops! Something went wrong.') }}
			</div>

			<ul class="mt-3 list-disc list-inside text-sm text-red-600">
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif
	{{-------------------------------------- Switches --------------------------------------}}
	<div class="border-b flex justify-between items-center">
		<label class="label-shared lg:text-lg">{{ ___('publish site') }}</label>
		<div class="text-[1.25rem]">
			<form action="{{ route('settings.publish') }}" method="POST">
				@csrf
				<button id="publish-switch" title="{{ ___('publish site') }}" class="switch @if(!setting('app.published')) {{ 'off' }} @endif">
				</button>
			</form>
		</div>
	</div>
	<div class="border-b flex justify-between items-center">
		<label class="label-shared lg:text-lg">{{ ___('enable e-shop') }}</label>
		<div class="text-[1.25rem]">
			<form action="{{ route('settings.toggleShop') }}" method="POST">
				@csrf
				<button id="publish-switch" title="{{ ___('enable e-shop') }}" class="switch @if(!setting('app.shop.enabled')) {{ 'off' }} @endif">
				</button>
			</form>
		</div>
	</div>
	{{-------------------------------------- Coupons --------------------------------------}}
	<div class="mt-10">
		<h2 class="label-shared lg:text-lg">{{ ___('coupons') }}</h2>
			<div id="coupons-wrapper" class="border grid grid-cols-5 gap-2 p-2">
			@foreach($coupons as $coupon)
				@php
					if((empty($coupon->expires_at) || (!empty($coupon->expires_at) && $coupon->expires_at->gt(\Carbon\Carbon::now()))) && (($coupon->used < $coupon->quantity && $coupon->quantity > 0) || ($coupon->quantity === 0))) {
						$extraCouponClass ='bg-gray-100 border-gray-400';
					} else {
						$extraCouponClass ='bg-red-200 border-red-500';
					}
				@endphp
				<div class="coupon border border-dotted p-2 flex justify-between items-center {{ $extraCouponClass }}">
					<div>
						<span class="font-bold">{{ $coupon->label }}</span> -{{ $coupon->value}}@if($coupon->type){{ '€' }}@else{{ '%' }}@endif{{ ' ('.$coupon->used }}@if($coupon->quantity){{ '/'.$coupon->quantity }}@endif{{ ')' }}
					</div>
					<a class="delete-coupon text-red-500" href="{{ route('coupons.delete', $coupon->id) }}"> <x-tabler-circle-x class="inline-block" /></a>
				</div>
			@endforeach
			<a id="add-coupon" href="{{ route('coupons.add') }}" class="bg-green-300 hover:bg-green-400 transition duration-300 rounded text-white text-center font-bold uppercase py-2">{{ ___('add coupon') }}</a>
		</div>
	</div>
	{{-------------------------------------- Shipping methods 2 --------------------------------------}}
	<div class="mt-10">
		<h2 class="label-shared lg:text-lg">{{ ___('shipping methods') }} : </h2>
		@foreach ($shippingMethods as $shippingMethod)
		<div class="px-2 my-6 border border-gray-400 bg-gray-100">
			<div class="flex justify-between items-center">
				<div>
					<h4 class="pt-2 pb-0">{{ $shippingMethod->label }} (Max {{ round($shippingMethod->max_weight / 1000, 3) }}Kg ) [ID:{{ $shippingMethod->id }}]</h4>
					<div class="mb-4 italic">{{ $shippingMethod->info }}</div>
				</div>
				<div>
					<a href="{{ route('shippingMethods.delete', $shippingMethod->id )}}" class="button-shared button-warning">{{ ___('delete') }}</a>
					<a href="{{ route('shippingMethods.edit', $shippingMethod->id )}}" class="button-shared">{{ ___('edit') }}</a>
				</div>
			</div>
			@php $previousWeight = 0; @endphp
			@if($shippingMethod->priceStops->isNotEmpty())
				{{-- To use as a minimum value for the new priceStop weight input --}}
				@php $firstStopWeight = $shippingMethod->priceStops->first()['weight'] @endphp
				{{ ___('between') }}
				{{ $previousWeight }}g
				{{ __('and') }}
				{{ $firstStopWeight }}g : {{ $shippingMethod->price }} € ({{ ___('base price') }})<a href="#"><br>
			@else
				@php $firstStopWeight = 0 @endphp
				{{ ___('unique price') }} : {{ $shippingMethod->price }} €
			@endif
			@foreach($shippingMethod->priceStops as $priceStop)
				{{ ___('between') }}
				<span class="font-bold text-green-900 bg-green-200">{{ $priceStop->weight }}g</span>
				{{ __('and') }}
				@if($shippingMethod->priceStops->get($loop->index + 1))
					{{ $shippingMethod->priceStops->get($loop->index + 1)->weight }}g
				@else
					{{ $shippingMethod->max_weight }}g
				@endif :
				{{ $priceStop->price }} € ({{ $priceStop->id }})<a href="{{ route('shippingMethods.deleteStop', $priceStop->id) }}"><x-tabler-x class="inline-block text-red-500"/></a><br>
				@php $previousWeight = $priceStop->weight; @endphp
			@endforeach
			<div class="border-b border-gray-400 mb-2"><h4 class="pb-0">{{ ___('add a stop-point') }}</h4></div>
			<form class="mb-2 flex justify-between" action="{{ route('shippingMethods.addStop', $shippingMethod->id) }}" method="POST">
				@csrf
				<div>
					<label for="shipping-weight-stop">{{ ___('add a new point at') }} </label><input type="number" name="weight" min={{ $firstStopWeight + 1 }} max="{{ $shippingMethod->max_weight - 1}}" id="shipping-weight-stop" />g,
					<label for="shipping-price-stop">{{ __('at price') }} </label><input type="number" step="0.01" name="price" min="{{ $shippingMethod->price + 0.01 }}" id="shipping-price-stop" />€
				</div>
				<input type="submit" class="button-shared self-center" value="{{ ___('add') }}" />
			</form>
		</div>
		@endforeach
		<div class="px-2 my-6 border border-gray-400 bg-gray-100">
			<div class="border-b border-gray-400 mb-2"><h4 class="pb-0">{{ ___('new shipping method') }}</h4></div>
			<form method="POST" action="{{ route('shippingMethods.add') }}" class="mb-2 w-full flex gap-8">
				@csrf
				<div class="flex-shrink-0">
					<label>{{ ___('name') }} : </label><input type="text" maxlength="127" name="label" class="input-shared"/><br>
					<label>{{ ___('base price') }} : </label><input type="number" step="0.01" min="0" name="price" class="input-shared"/><br>
					<label>{{ ___('maximum weight') }} (g) : </label><input type="number" min="0" name="max_weight" class="input-shared"/>
					<label>{{ ___('rule') }} : </label><select name="rule" class="input-shared">
						<option></option>
						<option value="national">{{ ___('national') }}</option>
						<option value="international">{{ ___('international') }}</option>
					</select>
				</div>
				<textarea placeholder="{{ ___('description') }}" class="w-full rounded-lg border border-gray-300" name="info"></textarea>
				<input type="submit" value="{{ ___('add') }}" class="button-shared self-center" />
			</form>
		</div>
	</div>
	{{-------------------------------------- Other settings --------------------------------------}}
	<form method="POST" action="{{ route('settings.update') }}">
		@csrf
		@method('patch')
		<div class="grid grid-cols-2 gap-x-4 gap-y-2 mt-10">
			{{-------------------------------------- Country list --------------------------------------}}
			<div class="col-span-2">
				@php
					$countryList = (setting('app.shipping.allowed-countries')) ? implode(',', setting('app.shipping.allowed-countries')) : '';
				@endphp
				<label for="shipping-allowed-countries" class="label-shared lg:text-lg">{{ __('Shipping to countries (Country codes separated by a coma, leave blank for international)') }} : </label>
				<input type="text" class="input-shared" id="shipping-allowed-countries" name="shipping-allowed-countries" value="{{ old('shipping-allowed-countries') ??  $countryList }}">
			</div>
			{{-------------------------------------- Paypal credentials --------------------------------------}}
			<div class="col-span-2  mt-8">
				<label for="paypal-client-id" class="label-shared lg:text-lg">{{ ___('paypal client ID') }} : </label>
				<input type="text" class="input-shared" id="paypal-client-id" name="paypal-client-id" value="{{ old('paypal-client-id') ?? setting('app.paypal.client-id') }}">
			</div>
			<div class="col-span-2">
				<label for="paypal-secret" class="label-shared lg:text-lg">{{ ___('paypal secret') }} : </label>
				<input type="text" class="input-shared" id="paypal-secret" name="paypal-secret" value="{{ old('paypal-secret') ?? setting('app.paypal.secret') }}">
			</div>
			<div class="col-span-2">
				<label for="paypal-sandbox" class="label-shared lg:text-lg">{{ ___('sandbox') }} : </label>
				<input type="checkbox" class="" id="paypal-sandbox" name="paypal-sandbox" value="true" {{ (old('paypal-sandbox') || setting('app.paypal.sandbox')) ? 'checked' : '' }}>
			</div>
			{{-------------------------------------- About --------------------------------------}}
			<div class="mt-8">
				<label class="label-shared lg:text-lg" for="about-0">{{ __('About: First Column') }}</label>
				<textarea class="input-shared h-96" id="about-0" name="about[]">{!! (Storage::disk('raw')->exists('about_0.txt')) ? Storage::disk('raw')->get('about_0.txt') : '' !!}</textarea>
			</div>
			<div class="mt-8">
				<label class="label-shared lg:text-lg" for="about-1">{{ __('About: Second Column') }}</label>
				<textarea class="input-shared h-96" id="about-1" name="about[]">{!! (Storage::disk('raw')->exists('about_1.txt')) ? Storage::disk('raw')->get('about_1.txt') : '' !!}</textarea>
			</div>
		</div>
		<div class="text-right mt-4">
			<input class="button-shared" type="submit" value="{{ ___('save') }}">
		</div>
	</form>
</x-layout-app>
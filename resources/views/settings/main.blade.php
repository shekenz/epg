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

	<x-section :title="___('general settings')">

		<x-slider-config :label="___('publish site')" settings="app.published" :title="___('publish site')" route="settings.publish"/>
		<x-slider-config :label="___('enable e-shop')" settings="app.shop.enabled" :title="___('enable e-shop')" route="settings.toggleShop" last/>

	</x-section>

	{{-------------------------------------- Coupons --------------------------------------}}
	<x-section :title="___('coupon settings')">

		<div id="coupons-wrapper" class="grid grid-cols-5 gap-2">
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

	</x-section>

	{{-------------------------------------- Shipping methods --------------------------------------}}
	<x-section :title="___('shipping methods')">
		@foreach ($shippingMethods as $shippingMethod)
		<div class="px-2 my-6 border border-gray-400 bg-gray-100">
			<div class="flex justify-between items-center">
				<div>
					<h4 class="pt-2 pb-0">{{ $shippingMethod->label }} (Max {{ round($shippingMethod->max_weight / 1000, 3) }}Kg)@if(config('app.env') === 'local') [ID:{{ $shippingMethod->id }}]@endif</h4>
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
				<span @if(config('app.env') === 'local') class="font-bold text-green-900 bg-green-200"@endif>{{ $priceStop->weight }}g</span>
				{{ __('and') }}
				@if($shippingMethod->priceStops->get($loop->index + 1))
					{{ $shippingMethod->priceStops->get($loop->index + 1)->weight }}g
				@else
					{{ $shippingMethod->max_weight }}g
				@endif :
				{{ $priceStop->price }} €@if(config('app.env') === 'local') ({{ $priceStop->id }})@endif<a href="{{ route('shippingMethods.deleteStop', $priceStop->id) }}"><x-tabler-x class="inline-block text-red-500"/></a><br>
				@php $previousWeight = $priceStop->weight; @endphp
			@endforeach
			<div class="border-b border-gray-400 mb-2"><h4 class="pb-0">{{ ___('add a stop-point') }}</h4></div>
			<form class="mb-2 flex justify-between" action="{{ route('shippingMethods.addStop', $shippingMethod->id) }}" method="POST">
				@csrf
				<div>
					<label for="shipping-weight-stop">{{ ___('add a new point at') }} </label><input type="number" name="weight" min={{ $firstStopWeight + 1 }} max="{{ $shippingMethod->max_weight - 1}}" id="shipping-weight-stop" class="input-base"/>g,
					<label for="shipping-price-stop">{{ __('at price') }} </label><input type="number" step="0.01" name="price" min="{{ $shippingMethod->price + 0.01 }}" id="shipping-price-stop" class="input-base"/>€
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
	</x-section>

	{{-------------------------------------- Acronyms --------------------------------------}}
	<x-section :title="___('acronyms')">

		<div class="border flex gap-2 p-2 flex-wrap">
			@foreach($acronyms as $acronym)
			<div class="border border-dotted py-2 px-4 border-gray-400 bg-gray-100">
				<span class="font-bold">{{ $acronym->label }}</span>
				<a class="delete-coupon text-red-500" href="{{ route('settings.deleteAcronym', $acronym->id) }}"> <x-tabler-circle-x class="inline-block" /></a>
			</div>
			@endforeach
		</div>

		<form method="POST" action="{{ route('settings.addAcronym') }}" class="m-2">
			@csrf
			<label for="acronym-label">{{ ___('add new acronym') }} :</label>
			<input class="input-base inline-block" type="text" name="label" id="acronym-label" />
			<input class="button-shared" type="submit" value="{{ ___('add') }}" />
		</form>

	</x-section>

	{{-------------------------------------- Other settings --------------------------------------}}
	<x-section :title="___('other settings')">
		
		<form method="POST" action="{{ route('settings.update') }}">
			@csrf
			@method('patch')

			{{-------------------------------------- Paypal credentials --------------------------------------}}
			<x-separator first>{{ ___('paypal settings') }}</x-separator>
			<x-input name="paypal-client-id" type="text" :label="___('paypal client ID')" value="{{ old('paypal-client-id') ?? setting('app.paypal.client-id') }}" />
			<x-input name="paypal-secret" type="text" :label="___('paypal secret')" value="{{ old('paypal-secret') ?? setting('app.paypal.secret') }}" />
			<div>
				<label for="paypal-sandbox">{{ ___('sandbox') }} : </label>
				<input type="checkbox" style="display:inline-block;" id="paypal-sandbox" name="paypal-sandbox" value="true" {{ (old('paypal-sandbox') || setting('app.paypal.sandbox')) ? 'checked' : '' }}>
			</div>
			{{-------------------------------------- About --------------------------------------}}
			<x-separator>{{ ___('about page settings') }}</x-separator>
			<x-textarea :label="___('first column')" name="about[]" wrapper-class="">{!! (Storage::disk('raw')->exists('about_0.txt')) ? Storage::disk('raw')->get('about_0.txt') : '' !!}</x-textarea>
			<x-textarea :label="___('second column')" name="about[]" wrapper-class="">{!! (Storage::disk('raw')->exists('about_1.txt')) ? Storage::disk('raw')->get('about_1.txt') : '' !!}</x-textarea>

			{{-------------------------------------- Terms --------------------------------------}}
			<x-separator>{{ ___('Terms & condition settings') }}</x-separator>
			<x-textarea :label="___('terms & conditions')" name="terms" wrapper-class="">{!! (Storage::disk('raw')->exists('terms.txt')) ? Storage::disk('raw')->get('terms.txt') : '' !!}</x-textarea>
			
			<div class="text-right mt-8 col-span-2">
				<input class="button" type="submit" value="{{ ___('save') }}">
			</div>
		</form>

	</x-section>

</x-layout-app>
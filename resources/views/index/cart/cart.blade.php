<x-index-layout lang="FR_fr">

	<x-slot name="scripts">
		<script src="https://www.paypal.com/sdk/js?client-id={{ setting('app.paypal.client-id') }}&currency=EUR&disable-funding=credit,card,bancontact,blik,eps,giropay,ideal,mercadopago,mybank,p24,sepa,sofort,venmo"></script>
		<script src="{{ asset('js/cart.js') }}" defer></script>
	</x-slot>

	<x-slot name="title">Cart ({{ CartHelper::count() }})</x-slot>
	
	@if($books->isNotEmpty())
		<div id="cart-wrapper" class="
			grid grid-cols-1
			md:grid-cols-2
			lg:grid-cols-3
			xl:grid-cols-2 
			gap-x-12
		">
			{{------------------------------------------------------------------- Articles wrapper (or cart) --}}
			<div id="cart" class="
				cart-list
				grid
				grid-cols-1
				lg:grid-cols-2
				lg:col-span-2
				lg:content-start
				xl:grid-cols-2
				xl:col-span-1
				gap-6
				border-black border-b md:border-b-0
				pb-4 md:pb-0
			">
			{{-- Global variable declaration --}}
			@php
				$total = 0;
				$totalWeight = 0;
			@endphp

			{{-- Articles component and global sub-total calculation --}}
			@foreach($books as $article)
				@php
					$total += $article['price'] * $article['cartQuantity'];
				@endphp
				@include('index.cart.article')
			@endforeach
			</div>

			{{------------------------------------------------------------------------------ Details wrapper --}}
			<div class="xl:grid xl:grid-cols-2 gap-x-12 gap-y-6">

				{{-- Shipping address form --}}
				<div>
					<form id="shipping-address-form" class="mt-6 md:mt-0">
						<h5 class="subdivision">{{ ___('contact information') }}</h5>
						<input class="shipping-address-input" type="text" name="surname" placeholder="{{ ___('last name') }}" autocomplete="family-name" maxlength="140" required /><span class="input-error">{{ __('This field is required') }}</span>
						<input class="shipping-address-input" type="text" name="given_name" placeholder="{{ ___('first name') }}" autocomplete="given-name" maxlength="140" required /><span class="input-error">{{ __('This field is required') }}</span>
						<input class="shipping-address-input" type="tel" name="phone_number" placeholder="{{ ___('phone') }}" autocomplete="tel" maxlength="15" /><span class="input-error">{{ __('This field is required') }}</span>
						<input class="shipping-address-input" type="email" name="email_address" placeholder="{{ ___('email') }}" autocomplete="email" maxlength="254" required /><span class="input-error">{{ __('This field is required') }}</span>
						<h5 class="mt-6 subdivision">{{ ___('shipping address') }}</h5>
						<input class="shipping-address-input" type="text" name="address_line_1" placeholder="{{ ___('address line 1') }}" autocomplete="shipping address-line1" maxlength="300" required /><span class="input-error">{{ __('This field is required') }}</span>
						<input class="shipping-address-input" type="text" name="address_line_2" placeholder="{{ ___('address line 2') }}" autocomplete="shipping address-line2" maxlength="300" /><span class="input-error">{{ __('This field is required') }}</span>
						<input class="shipping-address-input" type="text" name="admin_area_2" placeholder="{{ ___('city') }}" autocomplete="shipping address-level2" maxlength="120" required /><span class="input-error">{{ __('This field is required') }}</span>
						<input class="shipping-address-input" type="text" name="postal_code" placeholder="{{ ___('postcode') }}" autocomplete="shipping postal-code" maxlength="60" required /><span class="input-error">{{ __('This field is required') }}</span>
						<select class="shipping-address-input" id="country-input" name="country_code" autocomplete="country" required >
							@foreach (config('countries') as $code => $country)
								<option @if($code === "FR")selected @endif value="{{ $code }}">{{ $country }}</option>
							@endforeach
						</select><span class="input-error">{{ __('This field is required') }}</span>
					</form>
				</div>
				
				{{-- Order details --}}
				<div id="info" class="mt-6 xl:mt-0">

					{{-- Summarize list --}}
					<h5 class="subdivision">{{ ___('order summarize') }}</h5>
					<div id="summarize-list" class="my-6">
					@foreach($books as $book)
						@php $totalWeight += ($book->weight * $book->cartQuantity) @endphp
						<div id="summarize-book-{{ $book->id }}" class="my-1 flex justify-between items-end">
							<span>{{ $book->title }}
								<span class="parenthesis-block @if($book->cartQuantity <= 1) hidden @endif">(<span class="quantity-for-id-{{ $book->id }}">{{$book->cartQuantity}}</span>)</span>
							</span>
							<span>&nbsp;<span class="subtotal-for-id-{{ $book->id }}">{{ round($book->price * $book->cartQuantity, 2) }}</span>&nbsp;€</span>
						</div>
					@endforeach
					</div>
					
					{{-- Global subtotal --}}
					<h5 class="subdivision border-t flex justify-between">
						<span>{{ ___('subtotal') }}</span>
						<span><span id="cart-sub-total">{{ $total }}</span>&nbsp;€</span>
					</h5>

					{{-- Coupons --}}
					<form class="mt-6" id="coupon-form">
						<h5>{{ ___('add a coupon') }}<img src="{{ asset('img/loader2.gif') }}" class="hidden inline-block ml-2 w-4 h-4 align-middle" id="loader" /></h5>
						<div class="flex items-center">
							<input class="py-1 px-2 mt-1 w-full" type="text" id="coupon-input" placeholder="{{ ___('coupon') }}" autocomplete="off">
						</div>
						<span id="coupon-alert" class="text-red-500 text-sm italic hidden">{{ __('This coupon is not valid')}}</span>
						<h5 id="coupon-info" class="subdivision border-t flex justify-between mt-8 hidden">Test</h5>
					</form>

					{{-- Shipping form --}}
					@if($totalWeight <= 5000)
					<form class="mt-6" id="shipping-methods-form">
						<div id="international-shipping">
							<h5>{{ ___('shipping method') }}</h5>
							<div class="mt-1">
								@foreach($shippingMethods as $index => $shippingMethod)
									<div class="flex justify-between hidden shipping-method-wrapper {{ $shippingMethod->rule }}">
										<div>
											@php
												// We compact all priceStops as a JSON string to use in cart.js
												$shippingPrice = $shippingMethod->price;
												$dataPricesJson = '[';
												foreach ($shippingMethod->priceStops as $priceStop) {
													$dataPricesJson .= '{"price":'.$priceStop->price.',"weight":'.$priceStop->weight.'}';
													// If $priceStop is not the last item
													if(!($priceStop === $shippingMethod->priceStops[count($shippingMethod->priceStops)-1])) {
														$dataPricesJson .= ',';
													}
													if($totalWeight >= $priceStop->weight) {
														$shippingPrice = $priceStop->price;
													}
												}
												$dataPricesJson .= ']';
												// We add the calculated price of the first shipping method to the total
												if($loop->first) {
													$totalIncShipping = $total + $shippingPrice;
												}
											@endphp
											<input class="shipping-method selectable" id="shipping-method-{{ $index }}" type="radio" name="shipping-method" data-default-price="{{ $shippingMethod->price }}" data-prices="{{ $dataPricesJson }}" value="{{ $shippingMethod->id }}" />
											<label for="shipping-method-{{ $index }}">{{ __($shippingMethod->label) }}</label>
										</div>
										<span class="text-gray-300">{{ $shippingPrice }}&nbsp;€</span>
										<div class="shipping-info hidden">{{ $shippingMethod->info }}</div>
									</div>
								@endforeach
							</div>
						</div>
						<div id="shipping-info" class="mt-1 italic"></div>
					</form>
					@else
					<div class="mt-4 italic">
						@php $totalIncShipping = $total @endphp
						{{ __('front.overweight') }}
					</div>
					@endif


					{{-- Total --}}
					<h5 class="border-t subdivision flex justify-between mt-6">
						<span>{{ ___('total') }}</span>
						<span><span id="cart-total" data-raw-total="{{ $total }}">{{ $totalIncShipping }}</span>&nbsp;€</span>
					</h5>
					@if(setting('app.paypal.client-id') && setting('app.paypal.secret') && $totalWeight <= 5000)
					<div class="my-10 flex justify-end">
						<div class="w-48" id="paypal-checkout-button"></div>
					</div>
					@endif
				</div>
			</div>
		</div>
		
		{{-- Data helper--}}
		<div id="cart-total-weight" class="hidden" data-total-weight="{{ $totalWeight }}"></div>

	{{-- Empty cart wrapper --}}
	<div id="empty-cart-info" class="hidden flex place-items-center justify-center h-96">
	@else
	<div id="empty-cart-info" class="flex place-items-center justify-center h-96">
	@endif
		<h3 class="text-3xl text-center text-gray-300">{{ __('Your cart is empty') }}.</h3>
	</div>

</x-index-layout>
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
			text-[1.05rem]
			leading-[1.2rem]
			md:text-base
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
						<h5 class="subdivision">{{ __('Contact information') }}</h5>
						<input class="cart" type="text" placeholder="{{ __('Last name') }}" autocomplete="given-name" />
						<input class="cart" type="text" placeholder="{{ __('First name') }}" autocomplete="family-name" />
						<input class="cart" type="tel" placeholder="{{ __('Phone') }}" autocomplete="tel" />
						<input class="cart" type="email" placeholder="{{ __('Email') }}" autocomplete="email" />
						<h5 class="mt-6 subdivision">{{ __('Shipping address') }}</h5>
						<input class="cart" type="text" placeholder="{{ __('Adress 1') }}" autocomplete="shipping address-line1" />
						<input class="cart" type="text" placeholder="{{ __('Adress 2') }}" autocomplete="shipping address-line2" />
						<input class="cart" type="text" placeholder="{{ __('City') }}" autocomplete="shipping address-level2" />
						<input class="cart" type="text" placeholder="{{ __('Postcode') }}" autocomplete="shipping postal-code" />
						<select class="cart" id="country-input" name="country" autocomplete="country">
							@foreach (config('countries') as $code => $country)
								<option @if($code === "FR")selected @endif value="{{ $code }}">{{ $country }}</option>
							@endforeach
						</select>
						{{-- Billing adress form --}}
						<div class="hidden">
							<div class="mt-6">
								<input class="" type="checkbox" id="show-billing-address" /><label for="show-billing-address" >{{ __('Different billing address') }}</label>
							</div>
							<h5 class="mt-6 subdivision">{{ __('Billing address') }}</h5>
							<input class="cart" type="text" placeholder="{{ __('Adress 2') }}" />
							<input class="cart" type="text" placeholder="{{ __('City') }}" />
							<input class="cart" type="text" placeholder="{{ __('Postcode') }}" />
							<input class="cart" type="text" placeholder="{{ __('Country') }}" />
							<input class="cart" type="text" placeholder="{{ __('Adress 1') }}" />
						</div>
					</form>
				</div>
				
				{{-- Order details --}}
				<div id="info" class="mt-6 xl:mt-0">

					{{-- Summarize list --}}
					<h5 class="subdivision">{{ __('Order summarize') }}</h5>
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
						<span>{{ __('Subtotal') }}</span>
						<span><span id="cart-sub-total">{{ $total }}</span>&nbsp;€</span>
					</h5>

					{{-- Coupons --}}
					<form class="mt-6" id="coupon-form">
						<h5>{{ __('Add a coupon') }}<img src="{{ asset('img/loader2.gif') }}" class="hidden inline-block ml-2 w-4 h-4 align-middle" id="loader" /></h5>
						<div class="flex items-center">
							<input class="py-1 px-2 mt-1 w-full" type="text" id="coupon-input" placeholder="{{ __('Coupon') }}" autocomplete="off">
						</div>
						<span id="coupon-alert" class="text-red-500 text-sm italic hidden">{{ __('This coupon is not valid')}}</span>
						<h5 id="coupon-info" class="subdivision border-t flex justify-between mt-8 hidden">Test</h5>
					</form>

					{{-- Shipping form --}}
					<form class="mt-6" id="shipping-form">
						<div id="national-shipping" class="flex justify-between py-1">
							<div> {{-- LEAVE  THAT DIV HERE IT'S THE INPUT + LABEL WRAPPER --}}
								@php // TODO refractor for all shipping method in once, weathe rit is internatinal or national
									$shippingPrice = $shippingMethods[0]->price;
									$dataPricesJson = '[';
									foreach ($shippingMethods[0]->priceStops as $priceStop) {
										$dataPricesJson .= '{"price":'.$priceStop->price.',"weight":'.$priceStop->weight.'}';
										// If $priceStop is not the last item
										if(!($priceStop === $shippingMethods[0]->priceStops[count($shippingMethods[0]->priceStops)-1])) {
											$dataPricesJson .= ',';
										}
										if($totalWeight > $priceStop->weight) {
											$shippingPrice = $priceStop->price;
										}
									}
									$dataPricesJson .= ']';
									// Since this is the default shipping method, we add the calculated price to the total
									$totalIncShipping = $total + $shippingPrice;
								@endphp
								<input class="shipping-method" id="shipping-method-national" type="radio" name="shipping-method" data-default-price="{{ $shippingMethods[0]->price }}" data-prices="{{ $dataPricesJson }}" value="{{ $shippingMethods[0]->id }}" checked />
								<label for="shipping-method-national">{{ __('Shipping') }}</label>
							</div>
							<span class="text-gray-300 highlight">{{ $shippingPrice }}&nbsp;€</span>
						</div>
						<div id="international-shipping" class="hidden">
						<h5>{{ __('Shipping method') }}</h5>
							<div class="mt-1">
								@foreach($shippingMethods as $index => $shippingMethod)
									@if($shippingMethod->id !== 1)
										<div class="flex justify-between">
											<div>
												@php // TODO refractor for all shipping method in once, weathe rit is internatinal or national
													$shippingPrice = $shippingMethod->price;
													$dataPricesJson = '[';
													foreach ($shippingMethod->priceStops as $priceStop) {
														$dataPricesJson .= '{"price":'.$priceStop->price.',"weight":'.$priceStop->weight.'}';
														// If $priceStop is not the last item
														if(!($priceStop === $shippingMethod->priceStops[count($shippingMethod->priceStops)-1])) {
															$dataPricesJson .= ',';
														}
														if($totalWeight > $priceStop->weight) {
															$shippingPrice = $priceStop->price;
														}
													}
													$dataPricesJson .= ']';
												@endphp
												<input class="shipping-method selectable" id="shipping-method-{{ $index }}" type="radio" name="shipping-method" data-default-price="{{ $shippingMethod->price }}" data-prices="{{ $dataPricesJson }}" value="{{ $shippingMethod->id }}" />
												<label for="shipping-method-{{ $index }}">{{ __($shippingMethod->label) }}</label>
											</div>
											<span class="text-gray-300">{{ $shippingPrice }}&nbsp;€</span>
										</div>
									@endif
								@endforeach
							</div>
						</div>
					</form>

					{{-- Total --}}
					<h5 class="border-t subdivision flex justify-between mt-6">
						<span>{{ __('Total') }}</span>
						<span><span id="cart-total" data-raw-total="{{ $total }}">{{ $totalIncShipping }}</span>&nbsp;€</span>
					</h5>
					@if(setting('app.paypal.client-id') && setting('app.paypal.secret'))
						<div class="w-48" id="paypal-checkout-button"></div>
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
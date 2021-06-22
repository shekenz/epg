<x-index-layout lang="FR_fr">

	<x-slot name="scripts">
		<script src="{{ asset('js/cart.js') }}" defer></script>
	</x-slot>

	<x-slot name="title">Cart ({{ CartHelper::count() }})</x-slot>
	
		@if(isset($books))
		<div id="cart-wrapper" class="md:grid md:grid-cols-9 md:items-start">
			<div id="cart" class="cart-list md:col-span-5 md:mr-6 md:grid md:grid-cols-2 md:gap-6">
			@php
				$total = 0;
			@endphp
			@foreach($books as $article)
				@php
					$total += $article['price'] * $article['cartQuantity'];
				@endphp
				@include('index.cart.article')
			@endforeach
			</div>
			<div id="info" class="grid grid-cols-2 mt-6 md:col-start-6 md:col-span-4 md:mt-0">
				<p class="row-start-2 md:row-start-1 md:col-start-1">
					<a href="{{ route('cart.clear') }}" class="base-link">{{ __('Empty cart') }}</a>
				</p>
				<p class="col-start-2 text-right mb-6 md:row-start-1 md:col-start-2 md:text-left md:mr-6">
					{{ __('Total') }} : <span id="cart-total">{{ $total }}</span>€
				</p>
				<p class="row-start-2 col-start-2 text-right md:text-left mb-6 md:mr-6">
					<a href="{{ route('cart.shipping') }}" class="base-link">{{ __('Checkout') }}</a>
				</p>
			</div>
		</div>
		
		<div id="empty-cart-info" class="hidden place-items-center justify-center h-96">
		@else
		<div id="empty-cart-info" class="flex place-items-center justify-center h-96">
		@endif
			<h3 class="text-3xl text-center text-gray-300">
			{{ __('Your cart is empty') }}.
			</h3>
		</div>
</x-index-layout>
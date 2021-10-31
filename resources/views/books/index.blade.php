<x-index-layout>

	<x-slot name="title">Index</x-slot>

	<x-slot name="scripts">
		<script src="{{ asset('js/glide.js') }}" defer></script>
		<script src="{{ asset('js/add-to-cart.js') }}" defer></script>
	</x-slot>
	
	@foreach ($bookInfos as $glideIndex => $bookInfo)
		@include('books.book')
	@endforeach

	<div id="translation-helper" 
		data-added-message="{{ __('Article added to cart') }}"
		data-checkout-button="{{ __('Checkout cart') }}"
		data-stock-limit="{{ __('flash.cart.stockLimit') }}"
	></div>

</x-index-layout>
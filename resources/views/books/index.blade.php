<x-index-layout>

	<x-slot name="title">Index</x-slot>

	<x-slot name="scripts">
		<script src="{{ asset('js/glide.js') }}" defer></script>
		<script src="{{ asset('js/add-to-cart.js') }}" defer></script>
	</x-slot>
	
	@foreach ($books as $glideIndex => $book)
		@include('books.book')
	@endforeach

	<div id="added-flash" class="fixed z-[900] bottom-0 left-0 bg-gray-200 w-full flex items-center transition-all duration-500 overflow-y-hidden h-0 pl-[15%] hidden">
		<img src="{{ asset('img/frog_logo_books.svg') }}" class="h-28 inline-block"/><span class="mx-4">{{ __('Article added to cart') }}.</span><a class="button-lg" href="{{ route('cart') }}">{{ __('Checkout cart') }}</a>
	</div>

</x-index-layout>
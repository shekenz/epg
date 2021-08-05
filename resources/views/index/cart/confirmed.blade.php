<x-index-layout>
	<x-slot name="title">
		{{ __('Payment successful') }}
	</x-slot>

	<div class="mx-auto text-center">
		<img class="inline-block w-56 md:w-80 md:mt-10" src="{{ asset('img/frog_logo_books.svg') }}" alt="epg logo frog standing on a pile of books" />
		<p class="text-xl md:text-3xl mt-4 md:mt-10">{{ __('Thanks for your purchase')}}</p>
		<p class="text-xl md:text-3xl mt-4 md:mt-10">({{ __('You will receive an email soon') }})</p>
	</div>

</x-index-layout>


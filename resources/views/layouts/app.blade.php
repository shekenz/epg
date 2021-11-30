<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<title>Dashboard | {{ $title }}</title>
		<!-- Styles -->
		<link rel="preload" href="{{ asset('fonts/Nunito-Regular.ttf') }}" as="font" type="font/ttf" crossorigin>
		<link rel="preload" href="{{ asset('fonts/Nunito-Bold.ttf') }}" as="font" type="font/ttf" crossorigin>
		<link rel="preload" href="{{ asset('fonts/Roboto-Bold.ttf') }}" as="font" type="font/ttf" crossorigin>
		<link rel="prefetch" href="{{ asset('img/loader.svg')}}" as="image">
		<link rel="prefetch" href="{{ asset('img/loader_dark.svg')}}" as="image">
		<link rel="prefetch" href="{{ asset('img/loader_medium.svg')}}" as="image">
		<link rel="stylesheet" href="{{ asset('css/app.css') }}" type="text/css">
		<!-- Scripts -->
		<script>
			// On page load or when changing themes, best to add inline in `head` to avoid FOUC
			// If theme = dark in storage OR (if no theme in storage AND os is in darkmode)
			if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
				document.documentElement.classList.add('dark')
			} else {
				document.documentElement.classList.remove('dark')
			}
		</script>
		<script src="{{ asset('js/app.js') }}" defer></script>
		@if(isset($scripts))
			{{ $scripts }}
		@endif
    </head>
    <body>
        <div id="app">
            
			@include('layouts.dashboard-nav')

			<div class="flex">
				@include('layouts.dashboard-sidebar')

				<!-- Page Content -->
				<main>

					@if(session('flash'))
						<x-flash.back :message="session('flash')" class="{{ session('flash-type') }}"/>
					@endif
					@if( setting('app.paypal.sandbox'))
						<x-flash.back :message="__('flash.paypal.sandbox')" permanent class="warning"/>
					@endif
					@if( !setting('app.paypal.client-id') || !setting('app.paypal.secret'))
						<x-flash.back :message="__('flash.paypal.credentials')" permanent class="error"/>
					@endif

					{{ $slot ?? ''}}
				</main>

			</div>
        </div>

		<div id="pop-up-wrapper" class="hidden backdrop-blur-lg fixed top-0 left-0 w-full h-full z-[9001]">
			<div id="pop-up" class="pop-up border border-gray-400 rounded-lg shadow-lg py-8 px-10 bg-white max-w-[640px] m-auto mt-[30vh]">
				<div id="pop-inner-wrapper"></div>
				<div class="flex justify-between items-center mt-12">
					<button id="pop-up-close" type="button" class="button-shared block">{{ __('Close') }}</button>
					<img src="{{ asset('img/loader2.gif') }}" id="popup-loader" class="w-6 hidden"/>
					<button id="pop-up-button" type="button" class="button-shared block">{{ __('OK') }}</button>
				</div>
			</div>
		</div>

		<div id="img-popup-wrapper" class="text-gray-500 hidden bg-opacity-90 bg-black backdrop-blur-lg fixed top-0 left-0 w-full h-[100vh] z-[9002] flex justify-between items-center">
			<div id="img-popup-title" class="fixed top-8 text-2xl text-gray-100 font-bold text-center w-full">
			</div>
			<a id="previous-img-popup" class="hover:text-gray-100 transition cursor-pointer"><x-tabler-chevron-left class="h-20 w-20" /></a>
			<img id="img-popup-content" src="{{ asset('img/loader.svg') }}" alt="loading animation">
			<a id="next-img-popup" class="hover:text-gray-100 transition cursor-pointer"><x-tabler-chevron-right class="h-20 w-20" /></a>
			<a href="#" id="close-img-popup" class="hover:text-gray-100 transition fixed top-4 right-4"><x-tabler-x class="h-16 w-16" /></a>
			<div class="fixed bottom-6 text-center w-full">
				<div class="inline-block border border-gray-500 rounded-lg px-6 py-2 text-lg">
					{{ __('app.img-popup-help') }}
				</div>
			</div>
		</div>
		
		<!-- Hack to prevent transition from firing at load in Chrome -->
		<script> </script>
	</body>
</html>

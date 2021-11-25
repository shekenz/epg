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
		<link rel="stylesheet" href="{{ asset('css/app.css') }}" type="text/css">
		<!-- Scripts -->
		<script src="{{ asset('js/app.js') }}" defer></script>
		@if(isset($scripts))
			{{ $scripts }}
		@endif
	</head>
	<body class="font-dashboard antialiased">
		<div class="min-h-screen bg-gray-100">
			@include('layouts.navigation')

			<div id="save-loader" class="hidden fixed m-4 py-2 pl-4 pr-3 bg-white rounded-md border border-gray-200 shadow-sm">
				Saving&nbsp;<img src="{{ asset('img/loader2.gif') }}" class="inline-block h-6 w-6 ml-2">
			</div>
				
			<!-- Page Content -->
			<main>
				<div class="py-2 sm:py-8">
					<div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
						@if(session('flash'))
						<x-flash.back :message="session('flash')" class="flash-{{ session('flash-type') }}"/>
						@endif
						@if(setting('app.paypal.sandbox'))
						<x-flash.back :message="__('flash.paypal.sandbox')" permanent class="flash-warning"/>
						@endif
						@if( !setting('app.paypal.client-id') || !setting('app.paypal.secret'))
						<x-flash.back :message="__('flash.paypal.credentials')" permanent class="flash-error"/>
						@endif
						<div class="bg-white overflow-hidden shadow-sm rounded-md sm:rounded-lg">
							@if(isset($title))
							<div class="flex flex-row py-2 px-3 sm:py-4 sm:px-5 bg-white border-b border-gray-200 items-center">
								@isset($leftControls)
								<div id="left-controls" class="mr-4">
									{{ $leftControls }}
								</div>
								@endif
								<h3 class="text-lg flex-none font-bold">
									{{ $title }}
								</h3>
								<div class="flex-grow"></div>
								@isset($controls)
								<div id="controls" class="flex-none">
									{{ $controls }}
								</div>
								@endif
							</div>
							@endif
							<div class="m-1/2 md:m-2 xl:m-4">
								{{ $slot ?? ''}}
							</div>
						</div>
					</div>
				</div>
			</main>
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

		<div id="img-popup-wrapper" class="hidden bg-opacity-90 bg-black backdrop-blur-lg fixed top-0 left-0 w-full h-[100vh] z-[9002] flex justify-between items-center">
			<div id="img-popup-title" class="fixed top-8 text-2xl text-gray-100 font-bold text-center w-full">
			</div>
			<a id="previous-img-popup" class="text-gray-500 hover:text-gray-100 transition cursor-pointer"><x-tabler-chevron-left class="h-20 w-20" /></a>
			<img id="img-popup-content" src="/img/loader2.gif">
			<a id="next-img-popup" class="text-gray-500 hover:text-gray-100 transition cursor-pointer"><x-tabler-chevron-right class="h-20 w-20" /></a>
			<a href="#" id="close-img-popup" class="text-gray-500 hover:text-gray-100 transition fixed top-4 right-4"><x-tabler-x class="h-16 w-16" /></a>
			<div class="fixed bottom-6 text-center w-full text-gray-500">
				<div class="inline-block border border-gray-500 rounded-lg px-6 py-2 text-lg">
				Arrow keys to navigate, X to close
				</div>
			</div>
		</div>
		<!-- Hack to prevent transition from firing at load in Chrome -->
		<script> </script>
	</body>
</html>

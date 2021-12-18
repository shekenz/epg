<!DOCTYPE html>
<html lang="{{ config('app.langHTML') }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<title>Dashboard | {{ $title }}</title>
		<!-- Styles -->
		<link rel="prefetch" href="{{ asset('fonts/Roboto-Regular.ttf') }}" as="font" type="font/ttf" crossorigin>
		<link rel="prefetch" href="{{ asset('fonts/Roboto-Bold.ttf') }}" as="font" type="font/ttf" crossorigin>
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
		
		<!-- Hack to prevent transition from firing at load in Chrome -->
		<script> </script>
	</body>
</html>

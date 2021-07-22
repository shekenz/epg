<!doctype html>
<html lang="{{ config('app.locale') }}">
    <head>
		<script type="text/javascript">
			// On page load or when changing themes, best to add inline in `head` to avoid FOUC
			// If theme = dark in storage OR (if no theme in storage AND os is in darkmode)
			if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
				document.documentElement.classList.add('dark')
			} else {
				document.documentElement.classList.remove('dark')
			}
		</script>
        <title>
            {{ config('app.name') }}
            @if(isset($title)) | {{ $title }} @endif
        </title>
        <meta charset=UTF-8>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

		<link rel="preload" href="{{ asset('fonts/MonumentGroteskTrial-Bold.woff') }}" as="font" type="font/woff">
		<link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
		<link rel="preload" href="{{ asset('fonts/MonumentGroteskTrial-Italic.woff') }}" as="font" type="font/woff">
		<link rel="preload" href="{{ asset('fonts/MonumentGroteskTrial-Medium.woff') }}" as="font" type="font/woff">
		<link rel="preload" href="{{ asset('fonts/MonumentGroteskTrial-MediumItalic.woff') }}" as="font" type="font/woff">
		<link rel="preload" href="{{ asset('fonts/MonumentGroteskTrial-Regular.woff') }}" as="font" type="font/woff">
		
		{{-- //TODO Fix the connection_refused error when deployed on VPS --}}
        {{-- @if(config('app.env') == 'local') --}}
            <link rel="stylesheet" href="{{ asset('css/index.css') }}">
			<script src="{{ asset('js/index.js') }}" defer></script>
        {{-- @else {{-- Cache bustin in production DISBLED BECAUSE OF CONNECTION REFUSED ERROR WHEN DEPLOYED ON VPS 
            <link rel="stylesheet" href="{{ asset(mix('css/index.css'), true) }}">
			<script src="{{ asset(mix('js/index.js'), true) }}" defer></script>
        @endif --}}
		@auth
			<script src="{{ asset('js/user-menu.js') }}" defer></script>
		@endauth
		@if(isset($scripts))
			{{ $scripts }}
		@endif
    </head>
    <body class="text-custom-md lg:text-custom">
		@if(session('flash'))
			<x-flash.default :message="session('flash')" class="flash-{{ session('flash-type') }}"/>
		@endif
		@auth
			@include('index.user-menu')
		@endauth

		@include('index.menu')
		@include('index.menu-under')
		<div id="content" class="mx-4 pb-16 mt-16 md:pb-12 md:mt-24 xl:mt-40 md:mx-12 xl:mx-20">
			{{ $slot }}
		</div>
		<div id="footer" class="fixed bottom-4 right-4  md:bottom-8 md:right-12 xl:bottom-12 xl:right-20">
			<a id="fun" href="{{ route('about') }}"><img  class="block dark:hidden w-10 md:w-14 xl:w-auto" srcset="{{ asset('img/logo.png') }} 1x, {{ asset('img/logo@2x.png') }} 2x" src="{{ asset('img/logo.png') }}" alt="epg logo"><img  class="hidden dark:block w-10 md:w-14 xl:w-auto" srcset="{{ asset('img/logo-dark.png') }} 1x, {{ asset('img/logo-dark@2x.png') }} 2x" src="{{ asset('img/logo-dark.png') }}" alt="epg logo"></a>
		</div>
		<div id="pop-up-wrapper" class="hidden bg-black bg-opacity-80 fixed top-0 left-0 w-full h-full z-[9001]">
			<div id="pop-up" class="pop-up border border-black py-8 px-10 bg-white max-w-[640px] m-auto mt-[30vh]">
				<p id="pop-up-message"></p>
				<button id="pop-up-close" type="button" class="button inverted m-auto mt-8 block">{{ __('Close') }}</button>
			</div>
		<div>
		<!-- Hack to prevent transition from firing at load in Chrome -->
		<script> </script>
    </body>
</html>
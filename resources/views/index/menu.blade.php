@php $cartCount = CartHelper::count(); @endphp

<div id="menu-wrapper">
	<div class="flex justify-between md:grid md:grid-cols-7" id="menu">
		<h1><a href="{{ route('about') }}" class="{{ (request()->routeIs('about')) ? 'active ' : '' }}menu-item">e.p.g.</a></h1>
		<div><a href="{{ route('index') }}" class="{{ (request()->routeIs('index')) ? 'active ' : '' }}menu-item">{{ __('books') }}</a></div>
		<div><a href="{{ route('messages') }}" class="{{ (request()->routeIs('messages')) ? 'active ' : '' }}menu-item">{{ __('contact') }}</a></div>
		@if(setting('app.shop.enabled'))
		<div><a id="cart-menu-item" href="{{ route('cart') }}" class="{{ (request()->routeIs('cart') || request()->routeIs('cart.success')) ? 'active ' : '' }}menu-item">{{ __('cart') }}<span id="cart-menu-count">{{ (boolval($cartCount)) ? ' ('.$cartCount.')' : ''}}</span></a></div>
		@endif
		@if(request()->user() || config('app.env') === 'local')
			<div class="md:col-start-7 justify-self-end "><a href="#">fr</a> / <a href="#">en</a></div>
		@endif
	</div>
</div>

{{-- Fixed menu is on a different stack context than body, hence not blending with main background. Adding an artificial background to fix the issue. --}}
<div id="artificial-background"></div>
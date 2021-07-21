@php $cartCount = CartHelper::count(); @endphp

<div id="menu-wrapper" class="menu-wrapper">
	<div id="menu" class="menu">
		<h1><a href="{{ route('about') }}" class="{{ (request()->routeIs('about')) ? 'active' : '' }} menu-item">e.p.g.</a></h1>
		<div><a href="{{ route('index') }}" class="{{ (request()->routeIs('index')) ? 'active' : '' }} menu-item">{{ (config('app.env') == 'local') ? 'shop' : 'books'}}</a></div>
		<div><a href="{{ route('messages') }}" class="{{ (request()->routeIs('messages')) ? 'active' : '' }} menu-item">contact</a></div>
		@if(request()->user() || config('app.env') == 'local')
			@if(setting('app.shop.enabled'))
			<div class="md:col-start-4"><a id="cart-menu-item" href="{{ route('cart') }}" class="{{ (request()->routeIs('cart') || request()->routeIs('cart.success')) ? 'active ' : '' }} menu-item">cart{{ (boolval($cartCount)) ? ' ('.$cartCount.')' : '' }}</a></div>
			@endif
			<div class="md:col-start-9 justify-self-end "><a href="#">fr</a> / <a href="#">en</a></div>
		@endif
	</div>
</div>
@php $cartCount = CartHelper::count(); @endphp

<div id="menu-wrapper-under" class="menu-wrapper">
	<div id="menu-under" class="menu">
		<h1><a href="{{ route('about') }}" class="menu-item-under">e.p.g.</a></h1>
		<div><a href="{{ route('index') }}" class="menu-item-under">{{ (config('app.env') == 'local') ? 'shop' : 'books'}}</a></div>
		<div><a href="{{ route('messages') }}" class="menu-item-under">contact</a></div>
		@if(request()->user() || config('app.env') == 'local')
			@if(setting('app.shop.enabled'))
			<div class="md:col-start-4"><a id="cart-menu-item-under" href="{{ route('cart') }}" class="menu-item-under">cart{{ (boolval($cartCount)) ? ' ('.$cartCount.')' : '' }}</a></div>
			@endif
			<div class="md:col-start-9 justify-self-end "><a href="#">fr</a> / <a href="#">en</a></div>
		@endif
	</div>
</div>
<nav id="menu">
	<div id="menu-left">
		<a class="nav-item" href="{{ route('index') }}">
			<x-tabler-arrow-left />&nbsp;{{ config('app.name') }}
		</a>
	</div>
	<div id="save-loader" class="flex items-center px-4 hidden">
		<img class="w-6 h-6 mr-2" src="{{ asset('img/loader.svg') }}" alt="animated loader" />{{ ___('saving') }}...
	</div>
	<div class="flex-grow"></div>
	<div id="menu-right">
		<a class="nav-item" href="{{ route('users.display', Auth::user()->id) }}">
			<x-tabler-user />&nbsp;{{ ___('my profile') }}
		</a>
		<a class="nav-item" href="#">
			<x-tabler-power />&nbsp;{{ ___('logout') }}
		</a>
		<div id="darkmode" class="switch darkmode off"></div>
	</div>
</nav>
<nav id="menu">
	<div id="menu-left">
		<a class="nav-item" href="{{ route('index') }}">
			<x-tabler-arrow-left />&nbsp;{{ config('app.name') }}
		</a>
	</div>
	<div class="flex-grow"></div>
	<div id="menu-right">
		<a class="nav-item" href="{{ route('dashboard') }}">
			<x-tabler-dashboard />&nbsp;{{ ___('dashboard') }}
		</a>
		<a class="nav-item" href="{{ route('users.display', Auth::user()->id) }}">
			<x-tabler-user />&nbsp;{{ ___('my profile') }}
		</a>
		<a class="nav-item" href="#">
			<x-tabler-power />&nbsp;Déconnexion
		</a>
		<div id="darkmode" class="switch darkmode off"></div>
	</div>
</nav>
<nav id="left-bar" class="side-bar">
	<a class="side-bar-item @if(request()->routeIs('books')) {{ 'active' }} @endif" href="{{ route('books') }}">
		<x-tabler-book />&nbsp;Livres
	</a>
	<a class="side-bar-item @if(request()->routeIs('media')) {{ 'active' }} @endif" href="{{ route('media') }}">
		<x-tabler-photo />&nbsp;Média
	</a>
	<a class="side-bar-item @if(request()->routeIs('orders')) {{ 'active' }} @endif" href="{{ route('orders') }}">
		<x-tabler-receipt />&nbsp;Commandes</a>
	<a class="side-bar-item @if(request()->routeIs('users')) {{ 'active' }} @endif" href="{{ route('users') }}">
		<x-tabler-users />&nbsp;Utilisateurs</a>
	<a class="side-bar-item @if(request()->routeIs('settings')) {{ 'active' }} @endif" href="{{ route('settings') }}">
		<x-tabler-settings />&nbsp;Paramètres
	</a>
	<div class="flex-grow"></div>
	<span class="text-gray-400 dark:text-gray-500 self-center m-2 text-sm">Created by <a class="underline" href="https://github.com/shekenz">Shekenz</a></span>
</nav>
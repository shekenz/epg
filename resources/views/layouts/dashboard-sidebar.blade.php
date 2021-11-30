<nav id="left-bar" class="side-bar">
	<a class="side-bar-item @if(request()->routeIs('books')) {{ 'active' }} @endif" href="{{ route('books') }}">
		<x-tabler-book />&nbsp;{{ ___('books') }}
	</a>
	<a class="side-bar-item @if(request()->routeIs('media')) {{ 'active' }} @endif" href="{{ route('media') }}">
		<x-tabler-photo />&nbsp;{{ ___('media') }}
	</a>
	<a class="side-bar-item @if(request()->routeIs('orders')) {{ 'active' }} @endif" href="{{ route('orders') }}">
		<x-tabler-receipt />&nbsp;{{ ___('orders') }}</a>
	<a class="side-bar-item @if(request()->routeIs('clients')) {{ 'active' }} @endif" href="{{ route('clients') }}">
		<x-tabler-mood-smile />&nbsp;{{ ___('clients') }}</a>
	<a class="side-bar-item @if(request()->routeIs('users')) {{ 'active' }} @endif" href="{{ route('users') }}">
		<x-tabler-users />&nbsp;{{ ___('users') }}</a>
	<a class="side-bar-item @if(request()->routeIs('settings')) {{ 'active' }} @endif" href="{{ route('settings') }}">
		<x-tabler-settings />&nbsp;{{ ___('settings') }}
	</a>
	<div class="flex-grow"></div>
	<span class="text-gray-400 dark:text-gray-500 self-center m-2 text-sm">Created by <a class="underline" href="https://github.com/shekenz">Shekenz</a></span>
</nav>
@props(['route' => 'dashboard' , 'active', 'icon'])

@php
if(!isset($icon)) { $icon = 'alert-circle'; }
@endphp

<a href="{{ route($route) }}" {{ $attributes->class([
		'side-bar-item',
		'active' => request()->routeIs($route),
	]) }}>
	<x-dynamic-component :component="'tabler-'.$icon" />&nbsp;{{ $slot }}
</a>
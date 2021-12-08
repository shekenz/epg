@props(['type' => 'default', 'optimisation', 'label' => 'no-label', 'medium', 'original'])

@php

	switch($type) {
		case 'error':
			$color = ['bg' => 'bg-red-200', 'icon' => 'text-red-500'];
			$icon = 'circle-x';
			$title = __('app.media.infos.not-found');
			break;
		case 'warning':
			$color = ['bg' => 'bg-yellow-200', 'icon' => 'text-yellow-500'];
			$icon = 'alert-triangle';
			$title = __('app.media.infos.not-resized');
			break;
		case 'success':
			$color = ['bg' => 'bg-green-200', 'icon' => 'text-green-500'];
			$icon = 'circle-check';
			break;
		default:
			$color = ['bg' => 'bg-gray-300', 'icon' => 'text-gray-600'];
			$icon = 'photo';
			break;
	}

@endphp

<a href="#" @isset($title)title="{{ $title }}"@endif class="
	inline-block
	{{$color['bg']}}
	rounded
	m-1
	px-2
	py-0.5
	font-bold
	opti-button
	no-underline
	text-primary-dark
	dark:text-primary-dark
	hover:text-primary-dark
	dark:hover:text-primary-dark
" data-opti="{{ $optimisation }}">
	<x-dynamic-component :component="'tabler-'.$icon" class="{{$color['icon']}} inline-block" />
	{{ ucfirst($label) }}
	@if(!isset($original))
	({{ round(Storage::disk('public')->size('uploads/'.$medium->filehash.'_'.$optimisation.'.'.$medium->extension)/1024) }} KB)
	@else
	({{ round(Storage::disk('public')->size('uploads/'.$medium->filehash.'.'.$medium->extension)/1024) }} KB)
	@endif
</a>
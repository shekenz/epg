@props(['bottom', 'align'])

@php
	if(isset($align))
	{
		switch($align)
		{
			case 'left' : $align = 'start'; break; 
			case 'right' : $align = 'end'; break;
			case 'start' : break;
			case 'end' : break;
			case 'center' : break;
			case 'around' : break;
			case 'evenly' : break;
			default: $align = 'between';
		}
	}
	else
	{
		$align = 'between';
	}
@endphp

<nav class="@isset($bottom) mt-8 @else mb-6 @endif flex gap-x-6 justify-{{ $align }} {{ $attributes->get('class') }}">
	{{ $slot }}
</nav>
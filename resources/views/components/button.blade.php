@props(['href', 'label', 'icon', 'title', 'warning', 'disabled'])

<a href="{{ $href }}" {{ $attributes->class([
		'icon' => isset($icon),
		'warning' => isset($warning),
		'disabled' => isset($disabled),
	])->merge(['class' => 'button']) }} @isset($title)title="{{ $title }}"@endif>
	@isset($icon)
		<x-dynamic-component :component="'tabler-'.$icon" />
	@else
		{{ $label }}
	@endif
</a>

@props(['href', 'label', 'icon', 'title', 'warning', 'disabled'])

<a href="{{ $href }}" {{ $attributes->class([
		'icon' => (isset($icon) && !isset($label)),
		'warning' => isset($warning),
		'disabled' => isset($disabled),
	])->merge(['class' => 'button items-center justify-center gap-x-2 inline-flex']) }} @isset($title)title="{{ $title }}"@endif>
	@if(isset($icon) && !isset($label))
		<x-dynamic-component :component="'tabler-'.$icon" />
	@else
		@isset($icon)<x-dynamic-component :component="'tabler-'.$icon" class="text-primary-super-light inline"/>@endif{{ $label }}
	@endif
</a>


@props(['href', 'method', 'label', 'icon', 'icon-right', 'title' => 'no title', 'warning', 'disabled', 'big', 'compact'])

<form
	action="{{ $href }}"
	method="POST"
	class="m-0 inline-block"
	@isset($confirm) onsubmit="return confirm('{{ addslashes($confirm) }}')" @endif {{-- addslashes to escape single quotes --}}
>
	@csrf

	@isset($method)
		@method($method)
	@endif

	@if($slot->isNotEmpty()) {{ $slot }} @endif

	<button
		{{-- Merge class conditionaly --}}
		{{ $attributes->class([
			'icon' => (isset($icon) && !isset($label)),
			'warning' => isset($warning),
			'disabled' => isset($disabled),
			'big' => isset($big),
			'compact' => isset($compact),
		])->merge(['class' => 'button items-center justify-center gap-x-2 inline-flex']) }}
		{{-- Set title attribute --}}
		@isset($title)title="{{ $title }}"@endif
	>
	{{-- Display icon only or icon + text --}}
	@if(isset($icon) && !isset($label))
		<x-dynamic-component :component="'tabler-'.$icon" />
	@else
		{{-- Determining icon's color with php tags --}}
		{{-- We can't use blade directives in sub-component props, it throw an exception --}}
		{{-- (probably because Laravel don't parse blade syntax in x-component tags) --}}
		@php $iconClass = (isset($warning)) ? 'text-red-200' : 'text-primary-super-light ' ; @endphp
		{{-- Display icon on left side of label --}}
		@if(isset($icon) && !isset($iconRight))<x-dynamic-component :component="'tabler-'.$icon" :class="'inline '.$iconClass"/>@endif
		{{-- label --}}
		{{ $label }}
		{{-- Display icon on right side of label --}}
		@if(isset($icon) && isset($iconRight))<x-dynamic-component :component="'tabler-'.$icon" :class="'inline '.$iconClass"/>@endif
	@endif
	</button>

</form>
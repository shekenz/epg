@props(['href', 'confirm', 'label', 'icon', 'title', 'method', 'warning'])

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

	@isset($icon)
		<button {{ $attributes->class(['warning' => isset($warning)])->merge(['class' => 'button icon']) }} title="{{ $title }}"><x-dynamic-component :component="'tabler-'.$icon" /></button>
	@else
		<button {{ $attributes->class(['warning' => isset($warning)])->merge(['class' => 'button']) }}>{{ $label }}</button>
	@endif
</form>
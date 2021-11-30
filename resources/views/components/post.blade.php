@props(['href', 'confirm', 'label', 'icon', 'title'])

<form
	action="{{ $href }}"
	method="POST"
	class="m-0 inline-block"
	@isset($confirm) onsubmit="return confirm('{{ $confirm }}');" @endif
>
	@csrf
	@isset($icon)
		<button class="button icon" title="{{ $title }}"><x-dynamic-component :component="'tabler-'.$icon" /></button>
	@else
		<button {{ $attributes->merge(['class' => 'button']) }}>{{ $label }}</button>
	@endif
</form>
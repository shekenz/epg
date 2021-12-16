@props(['name', 'id', 'label', 'checked' => false, 'disabled' => false, 'color' => 'blue', 'array'])

@php $vue = $attributes->has(':id') @endphp

<div class="my-1">
	<input
	@if(!$vue)
		id="@isset($id){{ $id }}@else{{ $name }}@endif"
	@else
		:id="{{ $attributes->get(':id') }}"
	@endif
	name="{{ $name }}@isset($array)[]@endif"
	type="checkbox"
	@if($checked) {{ 'checked' }} @endif
	@if($disabled) disabled @endif
	class="
		border-2
		border-purple-500
		dark:focus:ring-0
		dark:focus:outline-none
		{{ $color }}
	" {{ $attributes() }}>
	<label
		@if(!$vue)
			for="@isset($id){{ $id }}@else{{ $name }}@endif"
		@else
			:for="{{ $attributes->get(':id') }}"
		@endif
		class="cursor-pointer @if($disabled) disabled @endif"
	>{{ $label }}</label>
</div>
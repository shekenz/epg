@props(['name', 'label', 'checked' => false, 'disabled' => false, 'color' => 'blue'])

<div class="my-1">
	<input id="{{ $name }}" name="{{ $name }}" type="checkbox" value="1" @if($checked) {{ 'checked' }} @endif @if($disabled) disabled @endif class="
		border-2
		border-purple-500
		dark:focus:ring-0
		dark:focus:outline-none
		{{ $color }}
	">
	<label for="{{ $name }}" class="@if($disabled) disabled @endif">{{ $label }}</label>
</div>
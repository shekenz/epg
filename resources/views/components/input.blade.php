@props(['label' => 'no-label', 'name', 'inline', 'wrapper-class', 'disabled' => false])

<div class="
	mb-2
	@isset($wrapperClass)
	{{ $wrapperClass }}
	@endif
">

@php dump($disabled) @endphp

	<label for="{{ $name }}" class="
		p-1
		block
		@if($disabled)
		text-gray-400
		dark:text-gray-500
		@endif
		@if($attributes->has('label-class'))
		{{ $labelClass }}
		@endif
	">{{ $label }} : </label>

	<div class="{{ ($slot->isEmpty() || $disbabled) ?: 'border bg-red-400 border-red-400' }}">
	<input
		class="
			px-4
			w-full
			dark:border-gray-600
			dark:bg-gray-600
			border
			focus:ring-0
			@if($slot->isEmpty())
			border-gray-300
			focus:border-primary
			dark:focus:bg-gray-500
			dark:focus:border-gray-500
			@else
			border-transparent
			focus:border-transparent
			@endif
			shadow-tight-window
			dark:shadow-none
			@if($disabled)
			text-gray-400
			dark:text-gray-500
			bg-gray-100
			dark:bg-opacity-40
			dark:border-transparent
			@else
			text-primary-dark
			dark:text-white
			bg-white
			@endif
		"
		id="{{ $name }}"
		name="{{ $name }}"
		{{ $attributes }}
	/>
	@if($slot->isNotEmpty() && !$disabled)
		<span class="text-white px-1 italic text-sm">{{ $slot }}</span>
	@endif
	</div>
</div>
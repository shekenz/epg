@props(['label' => 'no-label', 'name', 'inline', 'wrapper-class'])

<div class="
	mb-2
	@isset($wrapperClass)
	{{ $wrapperClass }}
	@endif
">

	{{---------------------------- Note ----------------------------

		Here we can't use {{ }} blade directive to write an attribute on the fly for the sub-component <x-label>
		Hence we can't use attributes->merge() neither to merge parent attributes to sub-component
		We still want to use the 'disable' attribute for labels outside this component

		The trick was to make <x-label> both compatible with 'disabled' attribute and disabled slot
		Check out the first line of the <x-label> component :

			$disabled = (isset($disabled) || $attributes->has('disabled'));

		That means we can trigger <x-label> disabled state with both attributes or slot.
		We can use the attribute outside a component, and use the slot like we do here,
		when <x-label> is used inside a component as a sub-component

	--}}

	<x-label for="{{ $name }}">@if($attributes->has('disabled')) <x-slot name="disabled"></x-slot> @endif {{ $label }} : </x-label>

	<div class="{{ ($slot->isEmpty() || $attributes->has('disabled')) ?: 'border bg-red-400 border-red-400' }}">
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
			@if($attributes->has('disabled'))
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
	@if($slot->isNotEmpty() && !$attributes->has('disabled'))
		<span class="text-white px-1 italic text-sm">{{ $slot }}</span>
	@endif
	</div>
</div>
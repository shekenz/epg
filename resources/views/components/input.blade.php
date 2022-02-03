@props(['label', 'id', 'name', 'type' => 'text', 'inline', 'wrapper-class', 'disabled' => false, 'array', 'required' => false])


<div {{ $attributes->filter(function($value, $attr) { return ($attr == 'v-show'); }) }}
	class="
	@isset($inline)
	flex
	items-center
	w-full
	@else
	mb-2
	@endif
	@isset($wrapperClass)
	{{ $wrapperClass }}
	@endif
">

	@isset($label)
		<label @if($attributes->has(':disabled')) :class="{ 'disabled' : {{ $attributes->get(':disabled') }} }" @endif
			for="@isset($id){{ $id }}@else{{ $name }}@endif"
			class="
			p-1
			mr-2
			block
			whitespace-nowrap
			@if($disabled)
			text-gray-400
			dark:text-gray-500
			@endif
			@if($attributes->has('label-class'))
			{{ $labelClass }}
			@endif
		">{{ $label }} : @if($required)<span class="text-red-500 italic text-sm">({{ __('required') }})</span>@endif</label>
	@endif

	<div class="@if($slot->isNotEmpty() && !$disabled) border bg-red-400 border-red-400 @endif @isset($inline) w-full @endif">
	<input
		class="
			px-4
			w-full
			dark:border-gray-600
			dark:bg-gray-600
			border
			focus:ring-0
			@if($slot->isEmpty())
			border-gray-400
			focus:border-primary
			dark:focus:bg-gray-500
			dark:focus:border-gray-500
			@else
			border-transparent
			focus:border-transparent
			@endif
			shadow-tight-window
			dark:shadow-none
			text-primary-dark
			dark:text-white
			bg-white
		"
		id="@isset($id){{ $id }}@else{{ $name }}@endif"
		name="{{ $name }}@isset($array)[]@endif"
		type="{{ $type }}"
		{{ $attributes->filter(function($value, $attr) { return ($attr != 'v-show'); }) }}
		@if($disabled) disabled @endif
	/>
	@if($slot->isNotEmpty() && !$disabled)
		<span class="text-white px-1 italic text-sm">{{ $slot }}</span>
	@endif
	</div>
</div>
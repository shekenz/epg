@props(['wrapper-class', 'name', 'label' => 'no-label', 'disabled' => false])

<div class="{{ $wrapperClass }}">
	<label for="name" class="block p-1 @if($disabled) disabled @endif">{{ $label }} : </label>
	<div class="{{ (empty($error) || $attributes->has('disabled')) ?: 'border bg-red-400 border-red-400' }}">
		<textarea id="{{ $name }}" name="{{ $name }}" {{ $attributes }} @if($disabled) disabled @endif class="
			dark:bg-gray-600
			w-full
			h-72
			@isset($error)
			border-none
			focus:border-none
			@else
			border-gray-300
			focus:border-primary
			@endif
			dark:border-gray-600
			focus:outline-none
			focus:ring-0
			dark:focus:border-gray-500
			dark:focus:border-[6px]
			shadow-tight-window
			dark:shadow-none
		">{{ $slot }}</textarea>
		@if(isset($error) && !$attributes->has('disabled'))
			<span class="text-white px-1 italic text-sm">{{ $error }}</span>
		@endif
	</div>
</div>
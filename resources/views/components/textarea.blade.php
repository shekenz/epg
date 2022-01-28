@props(['wrapperClass' => '', 'name', 'label' => 'no-label', 'disabled' => false, 'error'])

<div class="{{ $wrapperClass }}">
	<label for="name" class="block p-1 @if($disabled) disabled @endif">{{ $label }} : </label>
	<div class="{{ (empty($error) || $attributes->has('disabled')) ?: 'border bg-red-400 border-red-400' }}">
		<textarea id="{{ $name }}" name="{{ $name }}" @if($disabled) disabled @endif {{ $attributes->class([
			'dark:bg-gray-600',
			'w-full',
			'border-none' => isset($error),
			'focus:border-none' => isset($error),
			'border-gray-400' => !isset($error),
			'focus:border-primary' => !isset($error),
			'dark:border-gray-600',
			'focus:outline-none',
			'focus:ring-0',
			'dark:focus:border-gray-500',
			'dark:focus:border-[6px]',
			'shadow-tight-window',
			'dark:shadow-none',
		]) }} {{ $attributes }}>{{ $slot }}</textarea>
		@if(isset($error) && !$attributes->has('disabled'))
			<span class="text-white px-1 italic text-sm">{{ $error }}</span>
		@endif
	</div>
</div>
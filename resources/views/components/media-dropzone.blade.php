@props(['label' => 'no-label', 'wrapper-class', 'disabled' => false])

<div class="mb-2 flex flex-col flex-50-50 @isset($wrapperClass){{$wrapperClass}}@endif">

	<label class="
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

	<div {{ $attributes->class(['
		border
		border-gray-300
		shadow-tight-window
		dark:shadow-none
		dark:border-gray-600
		dark:bg-gray-600
		flex
		gap-4
		p-4
		flex-wrap
		flex-grow
	'])->merge(['class' => 'dropzone']) }}
	style="min-height : calc(102px + 2rem);"
	>
		@if($slot->isNotEmpty())
			{{ $slot }}
		@else
		<div class="w-full placeholder flex justify-center items-center border-[4px] box-border border-gray-300 dark:border-gray-500 border-dashed">
			<span class="text-3xl text-gray-300 dark:text-gray-500">{{ $placeholder }}</span>
		</div>
		@endif
	</div>
</div>
@props(['close'])

<div
	id="pop-up-wrapper"
	class="
		bg-opacity-80
		dark:bg-opacity-80
		bg-white
		dark:bg-primary-dark
		backdrop-blur-lg
		fixed
		top-0
		left-0
		w-full
		h-full
		z-[9001]
	" {{ $attributes }}>
	<div id="pop-up" class="pop-up rounded-md shadow-lg max-w-[640px] m-auto mt-[30vh]">
		<h2 class="
			h-[2.5rem]
			bg-primary
			rounded-t-md
			text-white
			text-lg
			dark:shadow-dark
			flex
			items-center
		"><a @isset($close) @click.prevent="{{ $close }}" @endif>x</a></h2>
		<div id="pop-inner-wrapper" class="bg-white dark:bg-gray-700 border">
			<div id="pop-content">{{ $slot }}</div>
			<x-buttons>
				<x-button href="#" :label=" __('Close')" id="pop-up-close" />
				<x-loader class="w-8 h-8 text-primary"/>
				<x-button href="#" :label="__('OK')" id="pop-up-button" />
			</x-buttons>
		</div>
	</div>
</div>
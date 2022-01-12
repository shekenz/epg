@props(['title' => 'no title', 'next-label' => 'ok'])

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
	<div id="pop-up" class="pop-up shadow-lg max-w-[640px] m-auto mt-[30vh] rounded-md">
		<h2 class="
			h-[2.5rem]
			bg-primary
			rounded-t-md
			text-white
			text-lg
			dark:shadow-dark
			flex
			items-center
			px-4
		">{{ $title }}</h2>
		<div id="pop-inner-wrapper" class="bg-white dark:bg-gray-700 p-8 rounded-b-md">
			<div id="pop-content">{{ $slot }}</div>
			<x-buttons bottom>
				<a href="#" id="pop-up-close" class="button" @if($attributes->has('close'))@click.prevent="{{ $attributes->get('close')}}" @endif>{{ ___('cancel') }}</a>
				<x-loader id="pop-up-loader" class="w-8 h-8 text-primary hidden"/>
				<a id="pop-up-button" class="button" @if($attributes->has('next'))@click.prevent="{{ $attributes->get('next')}}" @endif>{{ $nextLabel }}</a>
			</x-buttons>
		</div>
	</div>
</div>
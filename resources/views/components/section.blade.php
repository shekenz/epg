@props(['title', 'return', 'foldable'])

<section {{ $attributes->class(['titled' => isset($title)]) }}>
	@isset($title)
		<h2 class="flex">
			@isset($return)
			<a class="button icon" href="{{ $return }}"><x-tabler-arrow-left /></a>
			@endif
			<span class="mx-4 @isset($return) ml-2 @endif">{{ $title }}</span>
			@isset($foldable)
			<div class="flex-grow"></div>
			<a class="cursor-pointer float-right block mr-2 text-primary-ultra-light hover:text-white" onclick="this.parentElement.nextElementSibling.classList.toggle('hidden')"><x-tabler-square-minus /></a>
			@endif
		</h2>
	@endif
	<div class="rounded-b-md p-8">
		{{ $slot }}
	</div>
</section>


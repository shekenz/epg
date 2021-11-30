@props(['title', 'return'])

<section {{ $attributes->class(['titled' => isset($title)]) }}>
	@isset($title)
		<h2>
			@isset($return)
			<a class="button icon" href="{{ $return }}"><x-tabler-arrow-left /></a>
			@endif
			<span class="mx-4 @isset($return) ml-2 @endif">{{ $title }}</span>
		</h2>
	@endif
	<div class="rounded-b-md p-4">
		{{ $slot }}
	</div>
</section>


@props(['title' => 'no-title', 'return', 'foldable'])

@php $hasTitle = ( isset($title) || $attributes->has(':title') ); @endphp

<section {{ $attributes->filter(function($val, $attr) { return !in_array($attr, [':title', '@click.prevent']); })->class(['titled' => $hasTitle]) }}>
	@if($hasTitle)
		<h2 class="flex">
			@isset($return)
			<a class="button icon" href="{{ $return }}" @if($attributes->has('@click.prevent')) {{ $attributes->filter(function($val, $attr) { return ( $attr == '@click.prevent' ); }) }}@endif><x-tabler-arrow-left /></a>
			@endif
			<span class="mx-4 @isset($return) ml-2 @endif">@php
				// We used php tags because it was impossible to escape those damn {{ }} blade tags in that situation
				echo ($attributes->has(':title')) ? '{{'.$attributes->get(':title').'}}' : $title ;			
			@endphp</span>
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


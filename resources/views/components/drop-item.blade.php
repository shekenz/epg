
@props(['src', 'src2x', 'input', 'medium-id'])

<div class="flex-shrink-0 cursor-grab">
	@isset($input)
		<input name="media[]" type="hidden" value="{{ $mediumId }}">
	@endif
	<img src="{{ $src }}" @isset($src2x) srcset="{{ $src }}, {{ $src2x }} 2x" @endif data-id="{{ $mediumId }}">
</div>
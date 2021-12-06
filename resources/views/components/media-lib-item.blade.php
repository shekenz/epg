@props(['src', 'src2x', 'medium'])

<a class="rounded-sm hover:bg-gray-200 dark:hover:bg-gray-600 no-underline text-primary-dark" href="{{ route('media.display', $medium->id) }}">
	<div class="text-center truncate p-2 md:p-3">

		<img class="m-auto" src="{{ $src }}" @isset($src2x) srcset="{{ $src }} 1x, {{ $src2x }} 2x"@endif>

		<span class="text-sm">{{ $medium->name }}.{{ $medium->extension }}</span>
	</div>
</a>
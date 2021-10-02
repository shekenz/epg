@php
	$classesToMerge = ['flash'];
	if($attributes->has('permanent')) {
		array_push($classesToMerge, 'permanent');
	}	
@endphp

<div id="flash" {{ $attributes->merge(['class' => implode(' ', $classesToMerge)]) }}>
		{{ $message }}
</div>
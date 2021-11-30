@props(['href', 'label'])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'button']) }}>{{ $label }}</a>

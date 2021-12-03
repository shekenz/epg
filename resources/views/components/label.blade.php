
@php $disabled = (isset($disabled) || $attributes->has('disabled')); @endphp

<label {{ $attributes->class(['text-gray-400 dark:text-gray-500' => $disabled])->merge(['class' => 'p-1 block'])}}>{{ $slot }}</label>
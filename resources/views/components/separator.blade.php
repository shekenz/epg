@props(['first'])

<h2 {{ $attributes->class(['pt-4' => empty($first)])->merge(['class' => 'text-lg border-b border-primary-dark dark:border-gray-500 my-4']) }}>{{ $slot }}</h2>
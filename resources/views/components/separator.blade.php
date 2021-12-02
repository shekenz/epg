@props(['first'])

<h2 {{ $attributes->class(['mt-6' => empty($first)])->merge(['class' => 'text-lg border-b border-primary-dark dark:border-gray-500 mb-4']) }}>{{ $slot }}</h2>
@props(['icon' => 'alert-triangle'])

<a class="text-red-400 hover:text-red-500 dark:text-red-400 dark:hover:text-red-500 cursor-help" title="{{ $slot }}"><x-dynamic-component :component="'tabler-'.$icon" /></a>
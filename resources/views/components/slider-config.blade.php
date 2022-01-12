@props(['label' => 'no-label', 'settings', 'title' => 'no-title', 'route'])

<div class="
		flex
		justify-between
		border-t
		@if($attributes->has('last'))
		border-b
		@endif
		border-background
		dark:border-gray-600
		py-2">
	<label class="@if(!setting($settings)) {{ 'text-gray-400 dark:txt-gray-500' }} @endif">{{ $label }}</label>
	<div>
		<form action="{{ route($route) }}" method="POST">
			@csrf
			<button title="{{ $title }}" class="switch @if(!setting($settings)) {{ 'off' }} @endif">
			</button>
		</form>
	</div>
</div>
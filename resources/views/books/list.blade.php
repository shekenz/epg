<x-app-layout>

	<x-slot name="title">
			{{ ___('books') }}
	</x-slot>

	<x-slot name="controls">
		@if( !empty($archived) )
			<a href="{{ route('books.archives') }}" class="button big">{{ ___('archived') }} ({{ $archived }})</a>
		@endif
		<a href="{{ route('books.create') }}" class="button big">{{ ___('new') }}</a>
	</x-slot>

	<x-slot name="scripts">
		<script src="{{ asset('js/books-reorder.js') }}" type="text/javascript" defer></script>
	</x-slot>

	<table class="big">
		<thead>
			<td class="w-8"></td>
			<td>{{ ___('title') }}</td>
			<td>{{ ___('author') }}</td>
			<td class="text-right">{{ ___('actions') }}</td>
		</thead>
		<tbody id="books-sortable">
		@foreach($bookInfos as $bookInfo)
			<tr data-id="{{ $bookInfo->id }}">
				<td><x-tabler-grip-vertical class="h-8 w-8 cursor-grab"/></td>
				<td>
					@php 
						$warnings = false;
						if($bookInfo->books->isEmpty()) {
							$warnings[] = __('No variation found ! Book will not be displayed on front page').'.';
						}
					@endphp

					@if($warnings)
						<a href="{{ route('books.display', $bookInfo->id) }}" class="icon warning" title="{{ implode("\n", $warnings) }}"><x-tabler-alert-triangle /></a>
					@endif
					<a href="{{ route('books.display', $bookInfo->id) }}">{{ $bookInfo->title }}</a></td>
				<td>{{ $bookInfo->author }}</td>
				
				<td class="text-right">
					<a class="button icon inline" title="{{ ___('archive') }}" href="{{ route('books.archives.store', $bookInfo->id)}}"><x-tabler-archive /></a>
					<a class="button icon inline" title="{{ ___('edit') }}" href="{{ route('books.edit', $bookInfo->id) }}"><x-tabler-edit /></a>
				</td>
			</tr>
		@endforeach
		</tbody>
	</table>
    
</x-app-layout>
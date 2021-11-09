<x-app-layout>

	<x-slot name="title">
			{{ ___('books') }}
	</x-slot>

	<x-slot name="controls">
		@if( !empty($archived) )
			<a href="{{ route('books.archived') }}" class="button-shared">{{ ___('archived') }} ({{ $archived }})</a>
		@endif
		<a href="{{ route('books.create') }}" class="button-shared">{{ ___('new') }}</a>
	</x-slot>

	<x-slot name="scripts">
		<script src="{{ asset('js/books-reorder.js') }}" type="text/javascript" defer></script>
	</x-slot>

	<table class="w-full app-table">
		<thead class="font-bold">
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
						// if($book->media->isEmpty()) {
						// 	$warnings[] = __('No media linked ! Book will not be displayed on front page').'.';
						// }
						// if(empty($book->price)) {
						// 	$warnings[] = __('No price found ! Book will not be sellable').'.';
						// }
					@endphp

					@if($warnings)
						<a href="{{ route('books.display', $bookInfo->id) }}" class="icon warning" title="{{ implode("\n", $warnings) }}"><x-tabler-alert-triangle />{{ $bookInfo->title }}</a>
					@endif
					<a href="{{ route('books.display', $bookInfo->id) }}" class="default">{{ $bookInfo->title }}</a></td>
				<td>{{ $bookInfo->author }}</td>
				
				<td class="text-right">
					{{-- <a class="icon" title="{{ ___('archive') }}" href="{{ route('books.archive', $book->id)}}"><x-tabler-archive /></a> --}}
					<a class="mini-button" title="{{ ___('edit') }}" href="{{ route('books.edit', $bookInfo->id) }}"><x-tabler-edit /></a>
				</td>
			</tr>
		@endforeach
		</tbody>
	</table>
    
</x-app-layout>
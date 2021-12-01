<x-app-layout>

	{{-- head title tag --}}
	<x-slot name="title">
		{{ ___('books') }}
	</x-slot>

	<x-slot name="scripts">
		<script src="{{ asset('js/books-reorder.js') }}" type="text/javascript" defer></script>
	</x-slot>

	<x-section title="Test form">
		<form>
			<x-input type="text" name="test" label="Disabled" disabled value="truc"></x-input>
			<x-input type="text" name="test2" label="Normal" value="truc"></x-input>
			<x-input type="text" name="test2" label="Erreur" value="truc">Erreur sa mèèèèèère !!</x-input>
	</x-section>

	<x-section :title="___('books')" class="full">
		<x-buttons>
			<x-button :href="route('books.create')" :label="___('new')" class="big" />
			@if(!empty($archived))
			<x-button :href="route('books.archives')" :label="___('archived').' ('.$archived.')'" class="big" />
			@endif
		</x-buttons>
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
	</x-section>
    
</x-app-layout>
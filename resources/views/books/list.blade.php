<x-app-layout>

	{{-- head title tag --}}
	<x-slot name="title">
		{{ ___('books') }}
	</x-slot>

	<x-slot name="scripts">
		<script src="{{ asset('js/books-reorder.js') }}" type="text/javascript" defer></script>
	</x-slot>

	<x-section :title="___('books')" class="full">
		<x-buttons>
			<x-button icon="circle-plus" :href="route('books.create')" :label="___('new')" class="big" />
			@if(!empty($archived))
			<x-button icon="archive" :href="route('books.archives')" :label="___('archived').' ('.$archived.')'" class="big" />
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
						@if($bookInfo->books->isEmpty())
							<x-captions.warning>{{ __('app.warnings.missing-books') }}</x-captions.warning>
						@endif
						<a href="{{ route('books.display', $bookInfo->id) }}">{{ $bookInfo->title }}</a></td>
					<td>{{ $bookInfo->author }}</td>
					
					<td class="text-right">
						<x-button :title="___('archive')" :href="route('books.archives.store', $bookInfo->id)" icon="archive" />
						<x-button :title="___('edit')" :href="route('books.edit', $bookInfo->id)" icon="edit" />
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</x-section>
    
</x-app-layout>
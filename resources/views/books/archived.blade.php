<x-app-layout>

	<x-slot name="title">
		{{ ___('archived books') }}
	</x-slot>

	<x-slot name="leftControls">
		<a href="{{ route('books') }}" class="mini-button"><x-tabler-chevron-left /></a>
	</x-slot>

	<x-slot name="controls">
		<form method="POST" action="{{ route('books.archives.delete.all') }}" class="inline">
			@csrf
			<input type="submit" class="button-shared button-warning cursor-pointer" value="{{ ___('delete all') }}" onclick="return confirm('{{ __('Are you sure you want to permanently delete all the books').' ? '.__('This action is not reversible').'.'}}');">
		</form>
	</x-slot>

	<div class="m-4">
		<table class="border-collapse table-auto box-border w-full">
		<thead class="font-bold">
			<td>{{ ___('title') }}</td>
			<td>{{ ___('author') }}</td>
			<td>{{ ___('trashed') }}</td>
			<td>{{ ___('published by') }}</td>
			<td>{{ ___('action') }}</td>
		</thead>
		<tbody>
		@foreach($bookInfos as $bookInfo)
			<tr>
				<td>{{ $bookInfo->title }}</td>
				<td>{{ $bookInfo->author }}</td>
				<td class="hidden md:table-cell">{{ $bookInfo->deleted_at->diffForHumans() }}</td>
				<td class="hidden md:table-cell"><a href="{{ route('users.display', $bookInfo->user->id)}}" class="default">{{ $bookInfo->user->username }}</a></td>
				<td class="text-right">
					<form method="POST" action="{{ route('books.delete', $bookInfo->id) }}" class="inline">
						@csrf
						<a href="#" title="{{ ___('delete') }}" class="mini-button warning" onclick="
							event.preventDefault();
							if(confirm('{{ __('Are you sure you want to permanently delete the book').' '.$bookInfo->title.' ? '.__('All variations will also be deleted').'. '.__('This action is not reversible').'.'}}')) {
								this.closest('form').submit();
							}
						">
							<x-tabler-trash />
						</a>
					</form>
					<a class="mini-button" title="{{ ___('restore') }}" href="{{ route('books.archives.restore', $bookInfo->id) }}"><x-tabler-arrow-up-circle /></a>
				</td>
			</tr>
		@endforeach
		</tbody>
		</table>
	</div>

</x-app-layout>
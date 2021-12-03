<x-app-layout>

	<x-slot name="title">
		{{ ___('archived books') }}
	</x-slot>

	<x-slot name="controls">
		<form method="POST" action="{{ route('books.archives.delete.all') }}" class="inline">
			@csrf
			<input type="submit" class="button-shared button-warning cursor-pointer" value="{{ ___('delete all') }}" onclick="return confirm('?');">
		</form>
	</x-slot>

	<x-section class="full" :return="route('books')" :title="___('archived books')">
		<x-buttons align="right">
			<x-post warning :href="route('books.archives.delete.all')" :confirm="__('app.confirmations.delete-all-books')" :label="___('delete all')" class="big"/>
		</x-buttons>
		<table class="big">
		<thead>
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
					<x-post warning :href="route('books.delete', $bookInfo->id)" :confirm="__('app.confirmations.delete-book', ['book' => $bookInfo->title])" icon="trash" :title="___('delete')" />
					<x-button :href="route('books.archives.restore', $bookInfo->id)" :title="___('restore')" icon="arrow-up-circle"/>
				</td>
			</tr>
		@endforeach
		</tbody>
		</table>
	</x-section>

</x-app-layout>
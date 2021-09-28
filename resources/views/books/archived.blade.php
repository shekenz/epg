<x-app-layout>
    <x-slot name="title">
        {{ ___('archived books') }}
    </x-slot>

    <x-slot name="controls">
		<form method="POST" action="{{ route('books.deleteAll') }}" class="inline">
			@csrf
			<input type="submit" class="button-shared button-warning cursor-pointer" value="{{ ___('delete all') }}" onclick="return confirm('{{ __('Are you sure you want to permanently delete all the books').' ? '.__('This action is not reversible').'.'}}');">
		</form>
		<a href="{{ route('books') }}" class="button-shared">{{ ___('return to books') }}</a>
    </x-slot>

    <div class="m-4">
        <table class="border-collapse table-auto box-border w-full">
            {{-- <thead>
            </thead> --}}
            <tbody>
				<thead class="font-bold">
					<td>{{ ___('title') }}</td>
					<td>{{ ___('author') }}</td>
					<td>{{ ___('price') }}</td>
					<td>{{ ___('left') }}</td>
					<td>{{ ___('trashed') }}</td>
					<td>{{ ___('published by') }}</td>
					<td>{{ ___('action') }}</td>
				</thead>
				@foreach($books as $book)
                <tr>
                    {{-- <td class="hidden md:table-cell">{{ $book->id }}</td> --}}
                    <td>{{ $book->title }}</td>
					<td>{{ $book->author }}</td>
					<td>
					@if( !empty($book->price) )
						{{ $book->price }}€
					@endif
					</td>
					<td>{{ $book->quantity }}</td>
                    <td class="hidden md:table-cell">{{ $book->deleted_at->diffForHumans() }}</td>
                    <td class="hidden md:table-cell"><a href="{{ route('users.display', $book->user->id)}}" class="default">{{ $book->user->username }}</a></td>
                    <td class="text-right">
						<form method="POST" action="{{ route('books.delete', $book->id) }}" class="inline">
							@csrf
							<a href="#" title="{{ ___('delete') }}" class="icon warning" onclick="
								event.preventDefault();
								if(confirm('{{ __('Are you sure you want to permanently delete the book').' '.$book->title.' ? '.__('This action is not reversible').'.'}}')) {
									this.closest('form').submit();
								}
							">
								<x-tabler-trash />
							</a>
						</form>
						<a class="icon" title="{{ ___('restore') }}" href="{{ route('books.restore', $book->id) }}"><x-tabler-arrow-up-circle /></a>
					</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
</x-app-layout>
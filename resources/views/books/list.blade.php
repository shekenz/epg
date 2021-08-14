<x-app-layout>
    <x-slot name="title">
        {{ __('Books') }}
    </x-slot>

    <x-slot name="controls">
		@if( !empty($archived) )
		<a href="{{ route('books.archived') }}" class="button-shared">{{ __('Archived') }} ({{ $archived }})</a>
		@endif
        <a href="{{ route('books.create') }}" class="button-shared">{{ __('New') }}</a>
    </x-slot>

	<table class="w-full app-table">
		{{-- <thead>
		</thead> --}}
		<tbody>
			<thead class="font-bold">
				<td class="w-6"></td>
				<td>{{ __('Title') }}</td>
				<td>{{ __('Author') }}</td>
				<td>{{ __('Price') }}</td>
				<td>{{ __('Left') }}</td>
				<td>{{ __('Created') }}</td>
				<td>{{ __('Published by') }}</td>
				<td>{{ __('Actions') }}</td>
			</thead>
			@foreach($books as $book)
			<tr>
				{{-- <td class="hidden md:table-cell">{{ $book->id }}</td> --}}
				<td>
					@php 
						$warnings = false;
						if($book->media->isEmpty()) {
							$warnings[] = __('No media linked ! Book will not be displayed on front page').'.';
						}
						if(empty($book->price)) {
							$warnings[] = __('No price found ! Book will not be sellable').'.';
						}
					@endphp

					@if($warnings)
						<a href="{{ route('books.display', $book->id) }}" class="icon warning" title="{{ implode($warnings, "\n") }}"><x-tabler-alert-triangle /></a>
					@endif

					@if($book->pre_order)
						<span title="{{ __('Pre-order') }}"><x-tabler-clock class="inline-block" /></span>
					@endif
				</td>
				<td><a href="{{ route('books.display', $book->id) }}" class="default">{{ $book->title }}</a></td>
				<td>{{ $book->author }}</td>
				<td>
				@if( !empty($book->price) )
					{{ $book->price }} €
				@endif
				</td>
				
				<td>{{ $book->quantity }}</td>
				<td class="hidden md:table-cell">{{ $book->created_at->diffForHumans() }}</td>
				<td class="hidden md:table-cell"><a href="{{ route('users.display', $book->user->id)}}" class="default">{{ $book->user->username }}</a></td>
				<td class="text-right">
					
					<a class="icon" title="{{ __('Archive') }}" href="{{ route('books.archive', $book->id)}}"><x-tabler-archive /></a>
					<a class="icon" title="{{ __('Edit') }}" href="{{ route('books.edit', $book->id) }}"><x-tabler-edit /></a>
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
    
</x-app-layout>
<x-app-layout>
	<x-slot name="title">
			{{ ___('book').' : '.$bookInfo->title }}
	</x-slot>

	<x-slot name="controls">
		{{-- <a href="{{ route('books.archive', $book->id ) }}" class="button-shared">{{ ___('archive') }}</a> --}}
		<a href="{{ route('books.edit', $bookInfo->id ) }}" class="button-shared">{{ ___('edit') }}</a>
	</x-slot>

	<div class="grid grid-cols-4 gap-6">
		<div class="col-start-4">
			<p class="mb-4">
			<span class="font-bold">{{ __('ID') }} : </span>{{ $bookInfo->id }}<br>
			<span class="font-bold">{{ ___('title') }} : </span>{{ $bookInfo->title }}<br>
			<span class="font-bold">{{ ___('author') }} : </span>{{ $bookInfo->author }}<br>
			<span class="font-bold">{{ ___('width') }} : </span>
			@if( !empty($bookInfo->width))
				{{ $bookInfo->width }} mm
			@else
				{{ ___('empty') }}
			@endif
			<br>
			<span class="font-bold">{{ ___('height') }} : </span>
			@if( !empty($bookInfo->height))
				{{ $bookInfo->height }} mm
			@else
				{{ ___('empty') }}
			@endif
			<br>
			<span class="font-bold">{{ ___('pages count') }} : </span>
			@if( !empty($bookInfo->pages))
				{{ $bookInfo->pages }} pages
			@else
				{{ ___('empty') }}
			@endif
			<br>
			<span class="font-bold">{{ ___('cover') }} : </span>
			@if( !empty($bookInfo->cover))
				{{ $bookInfo->cover }}
			@else
				{{ ___('empty') }}
			@endif
			<br>
			<span class="font-bold">{{ ___('copies') }} : </span>
			@if( !empty($bookInfo->width))
				{{ $bookInfo->copies }}
			@else
				{{ ___('empty') }}
			@endif
			<br>
			<span class="font-bold">{{ ___('year') }} : </span>
			@if( !empty($bookInfo->year))
				{{ $bookInfo->width }}
			@else
				{{ ___('empty') }}
			@endif
			<br>
			<span class="font-bold">{{ ___('published by') }} : </span>{{ $bookInfo->user->username }}<br>
		</div>
		<div class="col-span-3 col-start-1 row-start-1">
			<h4>{{ ___('description') }} :</h4>
			<p class="mb-4">{!! nl2br(e($bookInfo->description)) !!}</p>
		</div>
		<div class="col-span-4">
			<h4>{{ ___('variations') }} :</h4>
			<table>
				<thead>
					<tr>
						<td>{{ ___('id') }}</td>
						<td>{{ ___('label') }}</td>
						<td>{{ ___('weight') }}</td>
						<td>{{ ___('stock') }}</td>
						<td>{{ ___('pre order') }}</td>
						<td>{{ ___('price') }}</td>
					</tr>
				</thead>
				<tbody>
				@foreach ($bookInfo->books as $book)
					<tr>
						<td>{{ $book->id }}</td>
						<td>{{ $book->label }}</td>
						<td>{{ $book->weight }} g</td>
						<td>{{ $book->stock }}</td>
						<td>{{ ___b($book->pre_order) }}</td>
						<td>{{ $book->price }} €</td>
				@endforeach
				</tbody>
			</table>
		</div>
	</div>
	<div>
	{{-- @if( $book->media->isEmpty() )
		<h4 class="text-red-500">{{ __('No media linked ! Book will not be displayed on front page') }}.</h4>
	@else
		<h4>{{ ___('attached media') }} :</h4>
		<p class="grid grid-cols-8 gap-4">
			@foreach ($book->media as $medium)
				<a href="{{ route('media.display', $medium->id )}}"><img src="{{ asset('storage/'.$medium->preset('thumb')) }}" srcset="{{ asset('storage/'.$medium->preset('thumb')) }} 1x, {{ asset('storage/'.$medium->preset('thumb2x')) }} 2x"></a>
			@endforeach
		</p>
	@endif --}}
	</div>
	    
</x-app-layout>
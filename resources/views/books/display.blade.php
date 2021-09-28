<x-app-layout>
    <x-slot name="title">
        {{ ___('book') }}
    </x-slot>

    <x-slot name="controls">
        <a href="{{ route('books.archive', $book->id ) }}" class="button-shared">{{ ___('archive') }}</a>
		<a href="{{ route('books.edit', $book->id ) }}" class="button-shared">{{ ___('edit') }}</a>
    </x-slot>

	<div class="grid grid-cols-4 gap-6">
		<div class="col-start-4">
			<p class="mb-4">
			<span class="font-bold">{{ __('ID') }} : </span>{{ $book->id }}<br>
			<span class="font-bold">{{ ___('title') }} : </span>{{ $book->title }}<br>
			<span class="font-bold">{{ ___('author') }} : </span>{{ $book->author }}<br>
			<span class="font-bold">{{ ___('width') }} : </span>
			@if( !empty($book->width))
				{{ $book->width }} mm
			@else
				{{ ___('empty') }}
			@endif
			<br>
			<span class="font-bold">{{ ___('height') }} : </span>
			@if( !empty($book->height))
				{{ $book->height }} mm
			@else
				{{ ___('empty') }}
			@endif
			<br>
			<span class="font-bold">{{ ___('pages count') }} : </span>
			@if( !empty($book->pages))
				{{ $book->pages }} pages
			@else
				{{ ___('empty') }}
			@endif
			<br>
			<span class="font-bold">{{ ___('cover') }} : </span>
			@if( !empty($book->cover))
				{{ $book->cover }}
			@else
				{{ ___('empty') }}
			@endif
			<br>
			<span class="font-bold">{{ ___('weight') }} : </span>
			{{ $book->weight }} g
			<br>
			<span class="font-bold">{{ ___('quantity') }} : </span>
				{{ $book->quantity }}
			<br>
			<span class="font-bold">{{ ___('price') }} : </span>
			@if( !empty($book->price))
				{{ $book->price }} €
			@else
				{{ ___('empty') }}
			@endif
			</p>
			<p class="mb-4">
				<span class="font-bold">{{ ___('published by') }} : </span>{{ $book->user->username}}<br>
				<span class="font-bold">{{ ___('created at') }} : </span>{{ $book->created_at }}<br>
				<span class="font-bold">{{ ___('last updated') }} : </span>{{ $book->updated_at }}<br>
			</p>
		</div>
		<div class="col-span-3 col-start-1 row-start-1">
			<h4>{{ ___('description') }} :</h4>
			<p class="mb-4">{!! nl2br(e($book->description)) !!}</p>
		</div>
	</div>
	<div>
	@if( $book->media->isEmpty() )
		<h4 class="text-red-500">{{ __('No media linked ! Book will not be displayed on front page') }}.</h4>
	@else
		<h4>{{ ___('attached media') }} :</h4>
		<p class="grid grid-cols-8 gap-4">
			@foreach ($book->media as $medium)
				<a href="{{ route('media.display', $medium->id )}}"><img src="{{ asset('storage/'.$medium->preset('thumb')) }}" srcset="{{ asset('storage/'.$medium->preset('thumb')) }} 1x, {{ asset('storage/'.$medium->preset('thumb2x')) }} 2x"></a>
			@endforeach
		</p>
	@endif
	</div>
	    
</x-app-layout>
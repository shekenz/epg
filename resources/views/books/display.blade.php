<x-app-layout>
    <x-slot name="title">
        {{ __('Book') }}
    </x-slot>

    <x-slot name="controls">
        <a href="{{ route('books.archive', $book->id ) }}" class="button-shared">{{ __('Archive') }}</a>
		<a href="{{ route('books.edit', $book->id ) }}" class="button-shared">{{ __('Edit') }}</a>
    </x-slot>

	<div class="grid grid-cols-4 gap-6">
		<div class="col-start-4">
			<p class="mb-4">
			<span class="font-bold">{{ __('ID') }} : </span>{{ $book->id }}<br>
			<span class="font-bold">{{ __('Title') }} : </span>{{ $book->title }}<br>
			<span class="font-bold">{{ __('Author') }} : </span>{{ $book->author }}<br>
			<span class="font-bold">{{ __('Width') }} : </span>
			@if( !empty($book->width))
				{{ $book->width }} mm
			@else
				{{ __('Empty') }}
			@endif
			<br>
			<span class="font-bold">{{ __('Height') }} : </span>
			@if( !empty($book->height))
				{{ $book->height }} mm
			@else
				{{ __('Empty') }}
			@endif
			<br>
			<span class="font-bold">{{ __('Pages count') }} : </span>
			@if( !empty($book->pages))
				{{ $book->pages }} pages
			@else
				{{ __('Empty') }}
			@endif
			<br>
			<span class="font-bold">{{ __('Cover') }} : </span>
			@if( !empty($book->cover))
				{{ $book->cover }}
			@else
				{{ __('Empty') }}
			@endif
			<br>
			<span class="font-bold">{{ __('Weight') }} : </span>
			{{ $book->weight }} g
			<br>
			<span class="font-bold">{{ __('Quantity') }} : </span>
				{{ $book->quantity }}
			<br>
			<span class="font-bold">{{ __('Price') }} : </span>
			@if( !empty($book->price))
				{{ $book->price }} €
			@else
				{{ __('Empty') }}
			@endif
			</p>
			<p class="mb-4">
				<span class="font-bold">{{ __('Published by') }} : </span>{{ $book->user->username}}<br>
				<span class="font-bold">{{ __('Created at') }} : </span>{{ $book->created_at }}<br>
				<span class="font-bold">{{ __('Last updated') }} : </span>{{ $book->updated_at }}<br>
			</p>
		</div>
		<div class="col-span-3 col-start-1 row-start-1">
			<h4>{{ __('Description') }} :</h4>
			<p class="mb-4">{!! nl2br(e($book->description)) !!}</p>
		</div>
	</div>
	<div>
	@if( $book->media->isEmpty() )
		<h4 class="text-red-500">{{ __('No media linked ! Book will not be displayed on front page') }}.</h4>
	@else
		<h4>{{ __('Attached media') }} :</h4>
		<p class="grid grid-cols-8 gap-4">
			@foreach ($book->media as $medium)
				<a href="{{ route('media.display', $medium->id )}}"><img src="{{ asset('storage/'.$medium->preset('thumb')) }}" srcset="{{ asset('storage/'.$medium->preset('thumb')) }} 1x, {{ asset('storage/'.$medium->preset('thumb2x')) }} 2x"></a>
			@endforeach
		</p>
	@endif
	</div>
	    
</x-app-layout>
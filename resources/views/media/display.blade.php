<x-app-layout>
    <x-slot name="title">
        {{ $medium->name }}.{{ $medium->extension }}
    </x-slot>

	<x-slot name="scripts">
		<script src="{{ asset('js/media-optimized.js') }}" defer></script>
	</x-slot>

	<x-slot name="controls">
		<form method="POST" action="{{ route('media.delete', $medium->id) }}" class="inline">
			@csrf
			<input type="submit" class="button-shared button-warning cursor-pointer" value="{{ ___('delete') }}" onclick="return confirm('{{__('Are you sure you want to permanently delete').' '.$medium->name.'.'.$medium->extension.' ? '.__('This action is not reversible').'.'}}');">
		</form>
	</x-slot>

	@if ($errors->any())
        <div class="mb-4" :errors="$errors">
            <div class="font-medium text-red-600">
                {{ __('Whoops! Something went wrong.') }}
            </div>

            <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
	<form action="{{ route('media.update', $medium->id) }}" method="POST" class="flex flex-row gap-x-4 mb-4 items-center">
		@csrf
		@method('patch')
		<label for="name" class="label-shared whitespace-nowrap">{{ ___('new name') }} : </label>
		<input class="input-shared" id="name" name="name" type="text" value="{{ old('name') ?? $medium->name }}" maxlength="64">
		<input class="button-shared" type="submit" value="{{ ___('rename') }}">
	</form>

	@if(Storage::disk('public')->exists('uploads/'.$medium->filename))
		<img id="frame" class="m-auto" src="{{ asset('storage/uploads/'.$medium->filename) }}" data-hash="{{ $medium->filehash }}" data-ext="{{ $medium->extension }}">
	@endif

	<div class="m-1">
		@foreach(config('imageoptimizer.uploads') as $key => $item)
			@if(Storage::disk('public')->exists('uploads/'.$medium->filehash.'_'.$key.'.'.$medium->extension))
				<?php $imagesize = getimagesize('storage/uploads/'.$medium->filehash.'_'.$key.'.'.$medium->extension); ?>
				@if($imagesize[0] < intval($item['width']) || $imagesize[1] < intval($item['height']))
				<a href="#" class="inline-block bg-yellow-200 rounded m-1 px-2 py-0.5 font-bold opti-button" data-opti="{{ $key }}">
					<x-tabler-alert-triangle class="text-yellow-500 inline-block" />
					{{ ucfirst($item['caption']) }}
					({{ round(Storage::disk('public')->size('uploads/'.$medium->filehash.'_'.$key.'.'.$medium->extension)/1024) }} KB)
				</a>
				@else
				<a href="#" class="inline-block bg-green-200 rounded m-1 px-2 py-0.5 font-bold opti-button" data-opti="{{ $key }}">
					<x-tabler-circle-check class="text-green-500 inline-block" />
					{{ ucfirst($item['caption']) }}
					({{ round(Storage::disk('public')->size('uploads/'.$medium->filehash.'_'.$key.'.'.$medium->extension)/1024) }} KB)
				</a>
				@endif
			@else
			<span class="inline-block bg-red-200 rounded m-1 px-2 py-0.5 font-bold">
				<x-tabler-circle-x class="text-red-500 inline-block" />
				{{ ucfirst($item['caption']) }}
			</span>
			@endif
		@endforeach
		@if(Storage::disk('public')->exists('uploads/'.$medium->filename))
			<a id="original" href="#" class="inline-block bg-gray-300 rounded m-1 px-2 py-0.5 font-bold" data-opti="{{ $key }}">
				<x-tabler-photo class="text-gray-600 inline-block" />
				Original
				({{ round(Storage::disk('public')->size('uploads/'.$medium->filehash.'.'.$medium->extension)/1024) }} KB)
			</a>
		@else
		<span class="inline-block bg-red-200 rounded m-1 px-2 py-0.5 font-bold" data-opti="{{ $key }}">
			<x-tabler-circle-x class="text-red-500 inline-block" />
			Original
		</span>
		@endif
	</div>

	@env('local')
	<div>
		<h4>{{ ___('file info') }}</h4>
		ID : {{ $medium->id }}<br>
		Hash : {{ $medium->filehash }}<br>
		Format : <span class="bg-gray-400 rounded px-2 py-0.5 font-bold uppercase text-white text-sm">{{ $medium->extension }}</span><br>
	</div>
	@endenv

	<div>
		@if( $medium->books->isEmpty() )
		<h4>{{ __('No linked books') }}.</h4>
		@else
			<h4>{{ ___('linked to') }} :</h4>
		@endif
		<table class="w-full app-table app-table-small">
		@foreach ($medium->books as $book)
			<tr>
				<td><a class="default" href="{{ route('books.display', $book->id) }}">{{ $book->title }}</a></td>
				<td>{{ __('by') }} {{ $book->author }}</td>
				@if( !empty($book->edition ))
					<td>{{ $book->edition }}</td>
				@else
					<td>({{ __('no edition') }})</td>
				@endif
				<td>{{ __('published by') }} <a class="default" href="{{ route('users.display', $book->user->id) }}">{{ $book->user->username }}</a></td>
				<td class="text-right w-8"><a class="icon" title="Break link" href="{{ route('media.break', [$medium, $book]) }}"><x-tabler-unlink /></a></td>
			</tr>
		@endforeach
		</table>
	</div>
</x-app-layout>
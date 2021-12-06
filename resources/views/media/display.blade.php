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

	<x-section :title="$medium->name.'.'.$medium->extension" class="full" :return="route('media')">

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

	<x-buttons class="items-center">
		<x-button :label="___('previous')" href="#" class="big" />
		<form action="{{ route('media.update', $medium->id) }}" method="POST" class="flex items-center gap-x-4">
			@csrf
			@method('patch')
			<x-input inline name="name" value="{{ old('name') ?? $medium->name }}" maxlength="64" />
			<input class="button big cursor-pointer" type="submit" value="{{ ___('rename') }}">
		</form>
		<x-post :label="___('delete')" :href="route('media.delete', $medium->id)" method="delete" :confirm="__('app.confirmations.delete-media', ['media' => $medium->name.'.'.$medium->extension])" class="big" warning/>
		<x-button :label="___('next')" href="#" class="big" />
	</x-buttons>

	@if(Storage::disk('public')->exists('uploads/'.$medium->filename))
		<img id="frame" class="m-auto" src="{{ asset('storage/uploads/'.$medium->filename) }}" data-hash="{{ $medium->filehash }}" data-ext="{{ $medium->extension }}">
	@endif

	<div class="m-1">
		@foreach(config('imageoptimizer.uploads') as $key => $item)
			@if(Storage::disk('public')->exists('uploads/'.$medium->filehash.'_'.$key.'.'.$medium->extension))
				<?php $imagesize = getimagesize('storage/uploads/'.$medium->filehash.'_'.$key.'.'.$medium->extension); ?>
				@if($imagesize[0] < intval($item['width']) || $imagesize[1] < intval($item['height']))
					<x-media-info-item type="warning" :optimisation="$key" :label="$item['caption']" :medium="$medium" />
				@else
					<x-media-info-item type="success" :optimisation="$key" :label="$item['caption']" :medium="$medium" />
				@endif
			@else
				<x-media-info-item type="error" :optimisation="$key" :label="$item['caption']" :medium="$medium" />
			@endif
		@endforeach
		@if(Storage::disk('public')->exists('uploads/'.$medium->filename))
			<x-media-info-item :optimisation="$key" :label="___('original')" :medium="$medium" original />
		@else
			<x-media-info-item type="error" :optimisation="$key" :label="___('original')" :medium="$medium" original />
		@endif
	</div>

	@env('local')
	<div>
		<x-separator>{{ ___('file info') }}</x-separator>
		ID : {{ $medium->id }}<br>
		Hash : {{ $medium->filehash }}<br>
		Format : <span class="bg-gray-400 rounded px-2 py-0.5 font-bold uppercase text-white text-sm">{{ $medium->extension }}</span><br>
	</div>
	@endenv

	<div>
		@if( $medium->books->isNotEmpty() )
			<x-separator>{{ ___('linked to following variations') }} :</x-separator>
			<table>
				<thead>
					<tr>
						<td>{{ ___('book') }}</td>
						<td>{{ ___('author') }}</td>
						<td>{{ ___('variation') }}</td>
						<td>{{ ___('published by') }}</td>
						<td>{{ ___('actions') }}</td>
					</tr>
				</thead>
				<tbody>
				@foreach ($medium->books as $book)
					<tr>
						<td><a class="default" href="{{ route('books.display', $book->bookInfo->id) }}">{{ $book->bookInfo->title }}</a></td>
						<td>{{ $book->bookInfo->author }}</td>
						<td><a class="default" href="{{ route('variations.edit', $book->id) }}">{{ $book->label }}</a></td>
						<td><a class="default" href="{{ route('users.display', $book->bookInfo->user->id) }}">{{ $book->bookInfo->user->username }}</a></td>
						<td class="text-right w-8">
							<x-button icon="unlink" :href="route('media.break', [$medium, $book])" :title="___('break link')" />
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		@endif
	</div>

	</x-section>

</x-app-layout>
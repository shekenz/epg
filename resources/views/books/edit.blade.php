<x-app-layout>
	<x-slot name="title">
		{{ ___('edit book').' "'.$bookInfo->title.'"' }}
	</x-slot>

	<x-slot name="leftControls">
		<a href="{{ route('books') }}" class="mini-button"><x-tabler-chevron-left /></a>
	</x-slot>

	<x-slot name="scripts">
		<script src="{{ asset('js/books-edit.js') }}" type="text/javascript" defer></script>
	</x-slot>
	
	<div class="m-4">
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

  	<form id="edit-form" action="{{ route('books.update', $bookInfo->id) }}" method="post" enctype="multipart/form-data" class="flex flex-col gap-y-2 md:grid md:grid-cols-4 lg:m-2 md:gap-x-4" autocomplete="off">
      @csrf
			@method('patch')
			<div>
        <label class="label-shared lg:text-lg" for="title">{{ ___('title') }} :</label>
        <input class="input-shared" id="title" name="title" type="text" value="{{ old('title') ?? $bookInfo->title }}" maxlength="128">
			</div>
			<div class="md:row-start-2">
        <label class="label-shared lg:text-lg" for="author">{{ ___('author') }} :</label>
        <input class="input-shared" id="author" name="author" type="text" value="{{ old('author') ?? $bookInfo->author }}" maxlength="64">
			</div>
			<div class="md:row-start-3">
        <label class="label-shared lg:text-lg" for="year">{{ ___('year') }} :</label>
        <input class="input-shared" id="year" name="year" type="number" value="{{ old('year') ?? $bookInfo->year }}" min="0" max="{{ now()->addYear(1)->year }}">
			</div>
			<div class="md:row-start-4">
				<label class="label-shared lg:text-lg" for="copies">{{ ___('copies') }} :</label>
				<input class="input-shared" id="copies" name="copies" type="number" value="{{ old('copies') ?? $bookInfo->copies }}">
			</div>
			<div class="md:col-start-2">
				<label class="label-shared lg:text-lg" for="width">{{ ___('width') }} (mm) :</label>
				<input class="input-shared" id="width" name="width" type="number" value="{{ old('width') ?? $bookInfo->width }}">
			</div>
			<div class="md:row-start-2 md:col-start-2">
				<label class="label-shared lg:text-lg" for="height">{{ ___('height') }} (mm) :</label>
				<input class="input-shared" id="height" name="height" type="number" value="{{ old('height') ?? $bookInfo->height }}">
			</div>
			<div  class="md:row-start-3 md:col-start-2">
				<label class="label-shared lg:text-lg" for="cover">{{ ___('cover') }} :</label>
				<input class="input-shared" id="cover" name="cover" type="text" value="{{ old('cover') ?? $bookInfo->cover }}" maxlength="32">
			</div>
			<div class="md:row-start-4 md:col-start-2">
				<label class="label-shared lg:text-lg" for="pages">{{ ___('pages count') }} :</label>
				<input class="input-shared" id="pages" name="pages" type="number" value="{{ old('pages') ?? $bookInfo->pages }}">
			</div>
			<div class="col-start-3 col-span-2 row-start-1 row-span-4 flex flex-col">
        <label class="label-shared lg:text-lg" for="description">{{ ___('description') }} :</label>
        <textarea id="description" class="input-shared flex-1" name="description">{{ old('description') ?? $bookInfo->description }}</textarea>
			</div>
			<input type="hidden" name="lang" value="fr">

			<div class="col-span-4 my-4 flex justify-end">
				<input class="button-shared w-full lg:w-auto px-4 py-2 cursor-pointer" type="submit"  value="{{ ___('save') }}">
			</div>

			</form>

			<h2 class="col-span-4 text-lg font-bold border-b border-gray-500 my-4">{{ ___('variations') }}</h2>

			<table class="mt-4 col-span-4">
				<thead class="font-bold">
					<tr>
						<td class="whitespace-nowrap"></td>
						<td class="whitespace-nowrap">{{ ___('label') }}</td>
						<td class="whitespace-nowrap">{{ ___('weight') }}</td>
						<td class="whitespace-nowrap">{{ ___('stock') }}</td>
						<td class="whitespace-nowrap">{{ ___('pre order') }}</td>
						<td class="whitespace-nowrap">{{ ___('price') }}</td>
						<td class="whitespace-nowrap">{{ ___('media') }}</td>
						<td class="text-right">{{ ___('actions') }}</td>
					</tr>
				</thead>
				<tbody id="variation-table-body" data-book-info-id="{{ $bookInfo->id }}">
				@foreach ($bookInfo->books as $key => $book)
					<tr data-id="{{ $book->id }}">
						<td><x-tabler-grip-vertical class="h-8 w-8 cursor-grab"/></td>
						<td class="whitespace-nowrap">
							{{ $book->label }}
						</td>
						<td class="whitespace-nowrap">
							{{ $book->weight }} g
						</td>
						<td>
							@isset($book->stock)
								{{ $book->stock }}
							@else
								0
							@endif
						</td>
						<td>
							{{ ___b($book->pre_order) }}
						</td>
						<td class="whitespace-nowrap">
							@isset($book->price)
								{{ $book->price }} €
							@else
								{{ $book->price.' € ('.___('base price').')' }}
							@endisset
						</td>
						<td>
							<div class="flex flex-wrap gap-1">
							@if($book->media->isNotEmpty())
								@foreach ($book->media as $medium)
									<img src="{{ asset('storage/'.$medium->preset('thumb')) }}" data-full-src="{{ asset('storage/'.$medium->preset('hd')) }}" class="inline-block h-[50px] w-[50px] hover-thumb">
								@endforeach
							@else
								<div class="inline-flex h-[50px] items-center">{{ ___('no linked medium') }}</div>
							@endif
							</div>
						</td>
						<td class="text-right whitespace-nowrap">
							<a class="mini-button" href="{{ route('variations.edit', $book->id) }}">
									<x-tabler-edit class="inline-block" />
							</a>
							<form class="inline-block" method="POST" action="{{ route('variations.delete', $book->id) }}">
								@csrf
								@method('delete')
								<button class="mini-button">
									<x-tabler-trash class="inline-block" />
								</button>
							</form>
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>

			<div class="col-span-4 my-8 flex justify-end">
				<a href="{{ route('variations.create', $bookInfo->id) }}" class="button-shared w-full lg:w-auto px-4 py-2 cursor-pointer">{{ ___('add variation') }}</a>
			</div>

  </div>

</x-app-layout>
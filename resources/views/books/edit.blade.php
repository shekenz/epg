<x-app-layout>

	@php $escapedTitle = ___('edit book').' "'.$bookInfo->title.'"'; @endphp
	<x-slot name="title">
		{{ $escapedTitle }}
	</x-slot>

	<x-slot name="scripts">
		<script src="{{ asset('js/books-edit.js') }}" type="text/javascript" defer></script>
	</x-slot>
	
	
	<x-section :title="$escapedTitle" :return="route('books')" class="full">

		@if($errors->any())
			<x-warning>{{ __('app.errors.form') }}</x-warning>
		@endif

  	<form id="edit-form" action="{{ route('books.update', $bookInfo->id) }}" method="post" enctype="multipart/form-data" autocomplete="off">
      @csrf
			@method('patch')

			<x-separator first>{{ ___('general informations') }}</x-separator>

			<div class="flex flex-col md:grid md:grid-cols-4 md:gap-x-8">

				<x-input name="title" type="text" :label="___('title')" wrapper-class="md:row-start-2" value="{{ old('title') ?? $bookInfo->title }}" maxlength="128">@error('title'){{$message}}@enderror</x-input>
				<x-input name="author" type="text" :label="___('author')" wrapper-class="md:row-start-3" value="{{ old('author') ?? $bookInfo->author }}" maxlength="64">@error('author'){{$message}}@enderror</x-input>
				<x-input name="year" type="text" :label="___('year')" wrapper-class="md:row-start-4" value="{{ old('year') ?? $bookInfo->year }}" min="0" max="{{ now()->addYear(1)->year }}" >@error('year'){{$message}}@enderror</x-input>
				<x-input name="copies" type="number" :label="___('copies')" wrapper-class="md:row-start-5" value="{{ old('copies') ?? $bookInfo->copies }}" min="0">@error('copies'){{$message}}@enderror</x-input>
				<x-input name="width" type="number" :label="___('width').' (mm)'" wrapper-class="md:row-start-2 md:col-start-2" value="{{ old('width') ?? $bookInfo->width }}" min="0">@error('width'){{$message}}@enderror</x-input>
				<x-input name="height" type="number" :label="___('height').' (mm)'" wrapper-class="md:row-start-3 md:col-start-2" value="{{ old('height') ?? $bookInfo->height }}" min="0">@error('height'){{$message}}@enderror</x-input>
				<x-input name="cover" type="text" :label="___('cover')" wrapper-class="md:row-start-4 md:col-start-2" value="{{ old('cover') ?? $bookInfo->cover }}" maxlength="32">@error('cover'){{$message}}@enderror</x-input>
				<x-input name="pages" type="number" :label="___('pages')" wrapper-class="md:row-start-5 md:col-start-2" value="{{ old('pages') ?? $bookInfo->pages }}" min="0">@error('pages'){{$message}}@enderror</x-input>

				<x-textarea name="description" :label="___('description')" wrapper-class="col-start-3 col-span-2 row-start-2 row-span-4" class="h-72">
					@error('description')
					<x-slot name="error">
						{{ $message }}
					</x-slot>
					@enderror
					{{ old('description') ?? $bookInfo->description }}
				</x-textarea>

			</div>
			
			<input type="hidden" name="lang" value="fr">

			<x-buttons bottom align="right">
				<input class="button big cursor-pointer" type="submit"  value="{{ ___('save') }}">
			</x-buttons>

		</form>

		<x-separator>{{ ___('variations') }}</x-separator>

		@if($bookInfo->books->isNotEmpty())
			<table>

				<thead>
					<tr>
						<td></td>
						<td>{{ ___('label') }}</td>
						<td>{{ ___('weight') }}</td>
						<td>{{ ___('stock') }}</td>
						<td>{{ ___('pre order') }}</td>
						<td>{{ ___('price') }}</td>
						<td>{{ ___('media') }}</td>
						<td class="text-right">{{ ___('actions') }}</td>
					</tr>
				</thead>

				<tbody id="variation-table-body" data-book-info-id="{{ $bookInfo->id }}">
				@foreach ($bookInfo->books as $key => $book)
						<tr data-id="{{ $book->id }}">
							<td><x-tabler-grip-vertical class="h-8 w-8 cursor-grab"/></td>
							<td>
								{{ $book->label }}
							</td>
							<td>
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
							<td>
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
										<img src="{{ asset('storage/'.$medium->preset('thumb')) }}" data-full-src="{{ asset('storage/'.$medium->preset('hd')) }}" data-index={{ $loop->index }} data-title="{{ $medium->name.'.'.$medium->extension }}" class="hover-thumb inline-block h-[50px] w-[50px] cursor-pointer">
									@endforeach
								@else
									<x-captions.missing-media>{{ ___('app.warnings.missing-media') }}</x-captions.missing-media>
								@endif
								</div>
							</td>
							<td class="text-right whitespace-nowrap">
								<x-button icon=edit :href="route('variations.edit', $book->id)" :title="__('edit')"/>
								<x-post warning icon=trash :href="route('variations.delete', $book->id)" :title="__('edit')" :confirm="__('app.confirmations.delete-variation', ['variation' => $book->label])" method="delete"/>
							</td>
						</tr>
				@endforeach
				</tbody>

			</table>
		@else
			<x-warning>{{ __('app.errors.no-variation') }}</x-warning>
		@endif

		<x-buttons bottom align="right">
			<x-button :href="route('variations.create', $bookInfo->id)" :label="___('add variation')" class="big" />
		</x-buttons>

		@if($bookInfo->trashedBooks->isNotEmpty())

			<x-separator>{{ ___('app.variations.deleted-waiting-list') }}</x-separator>

			<table>
				<thead>
					<tr>
						<td>{{ ___('label') }}</td>
						<td>{{ ___('weight') }}</td>
						<td>{{ ___('stock') }}</td>
						<td>{{ ___('pre order') }}</td>
						<td>{{ ___('price') }}</td>
						<td>{{ ___('media') }}</td>
						<td>{{ ___('order') }}</td>
						<td class="text-right">{{ ___('actions') }}</td>
					</tr>
				</thead>
				<tbody>
				@foreach ($bookInfo->trashedBooks as $key => $book)
					<tr data-id="{{ $book->id }}">
						<td>
							{{ $book->label }}
						</td>
						<td>
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
						<td>
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
									<img src="{{ asset('storage/'.$medium->preset('thumb')) }}" data-full-src="{{ asset('storage/'.$medium->preset('hd')) }}" data-index={{ $loop->index }} data-title="{{ $medium->name.'.'.$medium->extension }}" class="inline-block h-[50px] w-[50px] hover-thumb cursor-pointer">
								@endforeach
							@else
								<x-captions.missing-media />
							@endif
							</div>
						</td>
						<td>
							@if($book->orders->count() > 1)
								{{ $book->orders->count().' '.___('orders') }}
							@elseif($book->orders->count() > 0)
								<a href="{{ route('orders.display', $book->orders->first()->id) }}">{{ $book->orders->first()->order_id }}</a>
							@endif
						</td>
						<td class="text-right whitespace-nowrap">
							<x-button :href="route('variations.restore', $book->id)" icon="arrow-up-circle" :title="___('restore')"/>
							@if($book->orders->isEmpty())
								<x-post :href="route('variations.refresh', $book->id)" icon="recycle" :confirm="__('app.confirmations.refresh-variation', ['variation' => $book->label])" :title="___('refresh')" />
							@endif
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		@endif

	</x-section>

	<x-popups.image />

</x-app-layout>
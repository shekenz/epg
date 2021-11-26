<x-app-layout>
	<x-slot name='title'>
		{{ ___('edit variation').' "'.$book->label.'" '.__('from').' '.__('book').' "'.$book->bookInfo->title.'"' }}
	</x-slot>

	<x-slot name="scripts">
		<script src="{{ asset('js/media-library-dragdrop.js') }}" type="text/javascript" defer></script>
		<script src="{{ asset('js/variations-create.js') }}" type="text/javascript" defer></script>
		<script src="{{ asset('js/variations-edit.js') }}" type="text/javascript" defer></script>
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
	</div>

	<div class="m-4">
		@if($book->orders->isNotEmpty())
			<div class="text-red-500 italic">
				{{ __('app.variations-warning') }}
			</div>
		@endif
		<form id="edit-form" action="{{ route('variations.update', $book->id) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-4 justify-between items-stretch gap-x-4 gap-y-2 lg:m-2" autocomplete="off">
		@csrf
		@method('patch')
			<div>
				<label for="label" class="label-shared lg:text-lg">{{ ___('label') }} :</label>
				<input id="label" name="label" type="text" maxlength="128" class="input-shared" value="{{ old('label') ?? $book->label }}">
			</div>
			<div>
				<label for="weight" class="label-shared lg:text-lg">{{ ___('weight') }} :</label>
				<input id="weight" name="weight" type="number" min="0" class="input-shared" value="{{ old('weight') ?? $book->weight }}" @if($book->orders->isNotEmpty()) {{ 'disabled' }} @endif>
				@if($book->orders->isNotEmpty())
				<input name="weight" type="hidden" value="{{ $book->weight }}">
				@endif
			</div>
			<div>
				<label for="stock" class="label-shared lg:text-lg">{{ ___('stock') }} :</label>
				<input id="stock" name="stock" type="number" class="input-shared"  value="{{ old('stock') ?? $book->stock }}">
				<input id="stock-hidden" name="stock" type="hidden" disabled="true" value="{{ old('stock') ?? $book->stock }}">
				<div class="mt-1">
					<input class="" id="pre-order" name="pre_order" type="checkbox" value="1" @if(old('pre_order') ?? $book->pre_order) {{ 'checked' }} @endif><label for="pre-order" class="text-gray-500"> {{ ___('pre-order') }}</label>
				</div>
			</div>
			<div>
				<label for="price" class="label-shared lg:text-lg">{{ ___('price') }} :</label>
				<input id="price" name="price" type="number" min="0" step="0.01" class="input-shared" value="{{ old('price') ?? $book->price }}" @if($book->orders->isNotEmpty()) {{ 'disabled' }} @endif>
				@if($book->orders->isNotEmpty())
				<input name="price" type="hidden" value="{{ $book->price }}">
				@endif
			</div>

			{{-- Media Library --}}
			@if($media->isNotEmpty())
				<div class="col-span-4">
					<label class="label-shared lg:text-lg">{{ ___('linked media') }} :</label>
					<div id="media-link" class="dropzone input-mimic">
						@if($book->media->isEmpty())
						<div id="media-link-placeholder" class="placeholder flex m-3 justify-center items-center">
							<span class="text-3xl text-gray-300 font-bold">{{ __('Drop media from the library here')}}.</span>
						</div>
						@endif
						@php $input = true; @endphp
						@foreach( $book->media as $medium )
							@include('books.form-image')
						@endforeach
					</div>
				</div>
				
				<div class="col-span-4">
					<label class="label-shared lg:text-lg">{{ ___('media library') }} :</label>
					<div id="media-library" class="dropzone input-mimic">
						@if($media->isNotEmpty() && $media->diff($book->media)->isEmpty())
						<div id="media-library-placeholder" class="placeholder flex m-3 justify-center items-center">
							<span class="text-3xl text-gray-300 font-bold">{{ ___('move media here to unlink from book')}}.</span>
						</div>
						@endif
						@php $input = false; @endphp
						@foreach($media->diff($book->media) as $medium)
							@include('books.form-image')
						@endforeach
					</div>
				</div>
			@endif

			{{-- Upload Media --}}
			<div class="col-span-4">
				<label class="label-shared lg:text-lg">{{ __('Upload and link new media') }} :</label>
				<div class="input-mimic">
					<input type="file" name="files[]" accept=".jpg,.jpeg,.png,.gif" multiple>
				</div>
			</div>

			<div class="col-span-4 mt-2 lg:text-right">
				<input class="button-shared" type="submit" value="{{ ___('save') }}">
			</div>
	
		</form>
	</div>
	
</x-app-layout>
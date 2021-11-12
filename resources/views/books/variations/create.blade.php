<x-app-layout>
	<x-slot name='title'>
		{{ ___('add variation to book').' "'.$bookInfo->title.'"' }}
	</x-slot>

	<x-slot name="leftControls">
		<a href="{{ route('books.edit', $bookInfo->id) }}" class="mini-button"><x-tabler-chevron-left /></a>
	</x-slot>

	<x-slot name="scripts">
		<script src="{{ asset('js/media-library-dragdrop.js') }}" type="text/javascript" defer></script>
		<script src="{{ asset('js/variations-create.js') }}" type="text/javascript" defer></script>
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
		<form action="{{ route('variations.store', $bookInfo->id) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-4 justify-between items-stretch gap-x-4 gap-y-2 lg:m-2" autocomplete="off">
		@csrf
			<div>
				<label for="label" class="label-shared lg:text-lg">{{ ___('label') }} :</label>
				<input id="label" name="label" type="text" maxlength="128" class="input-shared">
			</div>
			<div>
				<label for="weight" class="label-shared lg:text-lg">{{ ___('weight') }} :</label>
				<input id="weight" name="weight" type="number" min="0" class="input-shared">
			</div>
			<div>
				<label for="stock" class="label-shared lg:text-lg">{{ ___('stock') }} :</label>
				<input id="stock" name="stock" type="number" class="input-shared">
				<input id="stock-hidden" name="stock" type="hidden">
				<div class="mt-1">
					<input class="" id="pre-order" name="pre_order" type="checkbox" value="1" @if(old('pre_order')) {{ 'checked' }} @endif><label for="pre-order" class="text-gray-500"> {{ ___('pre-order') }}</label>
				</div>
			</div>
			<div>
				<label for="price" class="label-shared lg:text-lg">{{ ___('price') }} :</label>
				<input id="price" name="price" type="number" min="0" step="0.01" class="input-shared">
			</div>

				<div class="col-span-4">
					<label class="label-shared lg:text-lg">{{ ___('linked media') }} :</label>
					<div id="media-link" class="dropzone input-mimic">
						<div id="media-link-placeholder" class="placeholder flex m-3 justify-center items-center">
							<span class="text-3xl text-gray-300 font-bold">{{ __('Drop media from the library here')}}.</span>
						</div>
					</div>
				</div>
				
				<div class="col-span-4">
					<label class="label-shared lg:text-lg">{{ ___('media library') }} :</label>
					<div id="media-library" class="dropzone input-mimic">
						@php $input = false; @endphp
						@foreach($media as $medium)
							@include('books.form-image')
						@endforeach
					</div>
				</div>

			<div class="col-span-4">
				<label class="label-shared lg:text-lg">{{ __('Upload and link new media') }} :</label>
				<div class="input-mimic">
					<input type="file" name="files[]" accept=".jpg,.jpeg,.png,.gif" multiple>
				</div>
			</div>

			<div class="col-span-4 mt-6 mb-4 lg:text-right">
				<input class="button-shared" type="submit" value="{{ ___('add') }}">
			</div>
	
		</form>
	</div>
	
</x-app-layout>
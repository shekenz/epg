<x-app-layout>
	<x-slot name="title">
		{{ ___('new book') }}
	</x-slot>

	<x-slot name="controls">
		<a href="{{ route('books') }}" class="button-shared">{{ ___('cancel') }}</a> 
	</x-slot>

	<x-slot name="scripts">
		<script src="{{ asset('js/media-library-dragdrop.js') }}" type="text/javascript" defer></script>
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

      <form action="{{ route('books.store') }}" method="post" enctype="multipart/form-data" class="flex flex-col gap-y-2 md:grid md:grid-cols-4 lg:m-2 md:gap-x-4" autocomplete="off">
        @csrf
				<div>
					<label class="label-shared lg:text-lg" for="title">{{ ___('title') }} :</label>
					<input class="input-shared" id="title" name="title" type="text" value="{{ old('title') }}" maxlength="128">
				</div>
				<div class="md:row-start-2">
					<label class="label-shared lg:text-lg" for="author">{{ ___('author') }} :</label>
					<input class="input-shared" id="author" name="author" type="text" value="{{ old('author') }}" maxlength="64">
				</div>
				<div class="md:row-start-3">
					<label class="label-shared lg:text-lg" for="year">{{ ___('year') }} :</label>
					<input class="input-shared" id="year" name="year" type="number" value="{{ old('year') }}" min="0" max="{{ now()->addYear(1)->year }}">
				</div>
				<div class="md:row-start-4">
					<label class="label-shared lg:text-lg" for="copies">{{ ___('copies') }} :</label>
					<input class="input-shared" id="copies" name="copies" type="number" value="{{ old('copies') }}">
				</div>
				<div class="md:col-start-2">
					<label class="label-shared lg:text-lg" for="width">{{ ___('width') }} (mm) :</label>
					<input class="input-shared" id="width" name="width" type="number" value="{{ old('width') }}">
				</div>
				<div class="md:row-start-2 md:col-start-2">
					<label class="label-shared lg:text-lg" for="height">{{ ___('height') }} (mm) :</label>
					<input class="input-shared" id="height" name="height" type="number" value="{{ old('height') }}">
				</div>
				<div class="md:row-start-3 md:col-start-2">
					<label class="label-shared lg:text-lg" for="cover">{{ ___('cover') }} :</label>
					<input class="input-shared" id="cover" name="cover" type="text" value="{{ old('cover') }}" maxlength="32">
				</div>
				
				<div class="md:row-start-4 md:col-start-2">
					<label class="label-shared lg:text-lg" for="pages">{{ ___('pages count') }} :</label>
					<input class="input-shared" id="pages" name="pages" type="number" value="{{ old('pages') }}">
				</div>
				<div class="col-start-3 col-span-2 row-start-1 row-span-4 flex flex-col">
					<label class="label-shared lg:text-lg" for="description">{{ ___('description') }} :</label>
					<textarea id="description" class="input-shared flex-1" name="description">{{ old('description') }}</textarea>
				</div>

				<h2 class="col-span-4 text-lg font-bold border-b border-gray-500 mb-4 mt-8">{{ ___('base variation') }}</h2>

				<div class="md:row-start-6 md:col-start-1">
					<label class="label-shared lg:text-lg" for="label">{{ ___('label') }} :</label>
					<input class="input-shared" id="label" name="label" type="text" value="{{ old('label') }}" max="128">
				</div>

				<div class="md:row-start-6 md:col-start-2">
					<label class="label-shared lg:text-lg" for="weight">{{ ___('weight') }} (g) :</label>
					<input class="input-shared" id="weight" name="weight" type="number" value="{{ old('weight') }}" min="0">
				</div>

				<div class="md:row-start-6 md:col-start-3">
					<label class="label-shared lg:text-lg" for="stock">{{ ___('stock') }} :</label>
					<input class="input-shared" id="stock" name="stock" type="number" min="0" value="{{ old('stock') }}">
					{{-- <input class="input-shared" id="quantity-hidden" name="quantity" type="hidden" disabled="true" value="0"> --}}
					<div class="mt-1">
						<input class="" id="pre-order" name="pre_order" type="checkbox" value="1" @if(old('pre_order')) {{ 'checked' }} @endif><label for="pre-order" class="text-gray-500"> {{ ___('pre-order') }}</label>
					</div>
				</div>

				<div class="md:row-start-6 md:col-start-4">
					<label class="label-shared lg:text-lg" for="price">{{ ___('price') }} :</label>
					<input class="input-shared" id="price" name="price" type="text" value="{{ old('price') }}" maxlength="10">
				</div>
				
				<input type="hidden" name="lang" value="fr">
				

			@if( $media->isNotEmpty() )
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
						@if($media->isEmpty())
							<div id="media-library-placeholder" class="placeholder flex m-3 justify-center items-center">
								<span class="text-3xl text-gray-300 font-bold">{{ __('Move media here to unlink from book')}}.</span>
							</div>
						@endif
						@foreach($media as $medium)
							@include('books.form-image')
						@endforeach
					</div>
				</div>
			@endif

			<div class="col-span-4">
				<label class="label-shared lg:text-lg">{{ __('Upload and link new media') }} :</label>
				<div class="input-mimic">
					<input type="file" name="files[]" accept=".jpg,.jpeg,.png,.gif" multiple>
				</div>
			</div>

			<div class="col-span-4 mt-2 lg:text-right">
            	<input class="button-shared w-full lg:w-auto" type="submit" value="{{ ___('create') }}">
			</div>
			
        </form>
    </div>

</x-app-layout>
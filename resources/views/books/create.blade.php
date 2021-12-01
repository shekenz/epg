<x-app-layout>
	<x-slot name="title">
		{{ ___('new book') }}
	</x-slot>

	<x-slot name="leftControls">
		<a href="{{ route('books') }}" class="mini-button"><x-tabler-chevron-left /></a>
	</x-slot>

	<x-slot name="controls">
		<a href="{{ route('books') }}" class="button-shared">{{ ___('cancel') }}</a> 
	</x-slot>

	<x-slot name="scripts">
		<script src="{{ asset('js/media-library-dragdrop.js') }}" type="text/javascript" defer></script>
	</x-slot>
	
	<x-section :title="___('new book')" :return="route('books')" class="full">

			<form action="{{ route('books.store') }}" method="post" enctype="multipart/form-data" class="flex flex-col gap-y-2 md:grid md:grid-cols-4 lg:m-2 md:gap-x-8" autocomplete="off">
				@csrf
				<x-input name="title" type="text" :label="___('title')" value="{{ old('title') }}" maxlength="128">@error('title'){{$message}}@enderror</x-input>
				<x-input name="author" type="text" :label="___('author')" wrapper-class="md:row-start-2" value="{{ old('author') }}" maxlength="64">@error('author'){{$message}}@enderror</x-input>
				<x-input name="year" type="text" :label="___('year')" wrapper-class="md:row-start-3" value="{{ old('year') }}" min="0" max="{{ now()->addYear(1)->year }}" >@error('year'){{$message}}@enderror</x-input>
				<x-input name="copies" type="number" :label="___('copies')" wrapper-class="md:row-start-4" value="{{ old('copies') }}" min="0">@error('copies'){{$message}}@enderror</x-input>
				<x-input name="width" type="number" :label="___('width').' (mm)'" wrapper-class="md:col-start-2" value="{{ old('width') }}" min="0">@error('width'){{$message}}@enderror</x-input>
				<x-input name="height" type="number" :label="___('height').' (mm)'" wrapper-class="md:row-start-2 md:col-start-2" value="{{ old('height') }}" min="0">@error('height'){{$message}}@enderror</x-input>
				<x-input name="cover" type="text" :label="___('cover')" wrapper-class="md:row-start-3 md:col-start-2" value="{{ old('cover') }}" maxlength="32">@error('cover'){{$message}}@enderror</x-input>
				<x-input name="pages" type="number" :label="___('pages')" wrapper-class="md:row-start-4 md:col-start-2" value="{{ old('pages') }}" min="0">@error('pages'){{$message}}@enderror</x-input>
				<x-textarea name="description" :label="___('description')" wrapper-class="col-start-3 col-span-2 row-start-1 row-span-4">
					@error('description')
					<x-slot name="error">
						{{ $message }}
					</x-slot>
					@enderror
					{{ old('description') }}
				</x-textarea>

				<h2 class="col-span-4 text-lg border-b border-gray-500 mb-4 mt-8">{{ ___('base variation') }}</h2>

				<x-input name="label" type="text" :label="___('label')" wrapper-class="md:row-start-6 md:col-start-1" value="{{ old('label') }}" maxlength="128">@error('label'){{$message}}@enderror</x-input>
				<x-input name="weight" type="number" :label="___('weight').' (g)'" wrapper-class="md:row-start-6 md:col-start-2" value="{{ old('weight') }}" min="0">@error('weight'){{$message}}@enderror</x-input>

				<div class="md:row-start-6 md:col-start-3">
					<x-input name="stock" type="number" :label="___('stock')" value="{{ old('stock') }}" min="0">@error('stock'){{$message}}@enderror</x-input>
					<div class="mt-1">
						<input class="" id="pre-order" name="pre_order" type="checkbox" value="1" @if(old('pre_order')) {{ 'checked' }} @endif><label for="pre-order" class="text-gray-500"> {{ ___('pre-order') }}</label>
					</div>
				</div>

				<x-input name="price" type="number" :label="___('price')" wrapper-class="md:row-start-6 md:col-start-4" value="{{ old('price') }}" min="0" step="0.01">@error('price'){{$message}}@enderror</x-input>

				
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

			<div class="col-span-4 mt-6 mb-4 lg:text-right">
				<input class="button big" type="submit" value="{{ ___('create') }}">
			</div>
			
		</form>

	</x-section>

</x-app-layout>
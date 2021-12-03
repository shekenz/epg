<x-app-layout>

	<x-slot name="title">
		{{ ___('new book') }}
	</x-slot>

	<x-slot name="scripts">
		<script src="{{ asset('js/media-library-dragdrop.js') }}" type="text/javascript" defer></script>
	</x-slot>
	
	<x-section :title="___('new book')" :return="route('books')" class="full">

		@if($errors->any())
			<x-warning>{{ __('app.errors.form') }}</x-warning>
		@endif

		<form action="{{ route('books.store') }}" method="post" enctype="multipart/form-data" autocomplete="off">
			@csrf

			<x-separator first>{{ ___('general informations') }}</x-separator>

			<div class="flex flex-col md:grid md:grid-cols-4 md:gap-x-8">

				<x-input name="title" type="text" :label="___('title')" wrapper-class="md:row-start-2" value="{{ old('title') }}" maxlength="128">@error('title'){{$message}}@enderror</x-input>
				<x-input name="author" type="text" :label="___('author')" wrapper-class="md:row-start-3" value="{{ old('author') }}" maxlength="64">@error('author'){{$message}}@enderror</x-input>
				<x-input name="year" type="text" :label="___('year')" wrapper-class="md:row-start-4" value="{{ old('year') }}" min="0" max="{{ now()->addYear(1)->year }}" >@error('year'){{$message}}@enderror</x-input>
				<x-input name="copies" type="number" :label="___('copies')" wrapper-class="md:row-start-5" value="{{ old('copies') }}" min="0">@error('copies'){{$message}}@enderror</x-input>
				<x-input name="width" type="number" :label="___('width').' (mm)'" wrapper-class="md:row-start-2 md:col-start-2" value="{{ old('width') }}" min="0">@error('width'){{$message}}@enderror</x-input>
				<x-input name="height" type="number" :label="___('height').' (mm)'" wrapper-class="md:row-start-3 md:col-start-2" value="{{ old('height') }}" min="0">@error('height'){{$message}}@enderror</x-input>
				<x-input name="cover" type="text" :label="___('cover')" wrapper-class="md:row-start-4 md:col-start-2" value="{{ old('cover') }}" maxlength="32">@error('cover'){{$message}}@enderror</x-input>
				<x-input name="pages" type="number" :label="___('pages')" wrapper-class="md:row-start-5 md:col-start-2" value="{{ old('pages') }}" min="0">@error('pages'){{$message}}@enderror</x-input>

				<x-textarea name="description" :label="___('description')" wrapper-class="col-start-3 col-span-2 row-start-2 row-span-4">
					@error('description')
					<x-slot name="error">
						{{ $message }}
					</x-slot>
					@enderror
					{{ old('description') }}
				</x-textarea>

			</div>

			<x-separator>{{ ___('base variation') }}</x-separator>

			<div class="flex flex-col md:grid md:grid-cols-4 md:gap-x-8">

				<x-input name="label" type="text" :label="___('label')" value="{{ old('label') }}" maxlength="128">@error('label'){{$message}}@enderror</x-input>
				<x-input name="weight" type="number" :label="___('weight').' (g)'" value="{{ old('weight') }}" min="0">@error('weight'){{$message}}@enderror</x-input>

				<div>
					<x-input name="stock" type="number" :label="___('stock')" value="{{ old('stock') }}" min="0">@error('stock'){{$message}}@enderror</x-input>
					<div class="mt-1">
						<input class="" id="pre-order" name="pre_order" type="checkbox" value="1" @if(old('pre_order')) {{ 'checked' }} @endif><label for="pre-order"> {{ ___('pre-order') }}</label>
					</div>
				</div>

				<x-input name="price" type="number" :label="___('price')" value="{{ old('price') }}" min="0" step="0.01">@error('price'){{$message}}@enderror</x-input>

			</div>
				

			@if( $media->isNotEmpty() )
				<x-separator>{{ ___('media') }}</x-separator>

				<div class="flex gap-x-4">

					<x-media-dropzone id="media-link" label="{{ ___('linked media') }}">
						<x-slot name="placeholder">{{ __('app.media.link-placeholder') }}</x-slot>
					</x-media-dropzone>

					<div class="flex flex-col justify-center items-center">
						<x-icon-left-arrow class="w-12 h-12 text-gray-600"/>
						<div class="h-6"></div>
						<x-icon-right-arrow class="w-12 h-12 text-gray-600"/>
					</div>

					<x-media-dropzone id="media-library" label="{{ ___('media library') }}">
						<x-slot name="placeholder">{{ __('app.media.library-placeholder') }}</x-slot>
						@foreach($media as $medium)
							<x-media-item :src="asset('storage/'.$medium->preset('thumb'))" :src2x="asset('storage/'.$medium->preset('thumb2x'))" :medium-id="$medium->id" />
						@endforeach
					</x-media-dropzone>
				</div>
			@endif

			<x-upload :label="__('Upload and link new media')" label-class="mt-3">
				{{ __('app.upload.limits', [
					'max_files' => ini_get('max_file_uploads'),
					'max_file_size' => ini_get('upload_max_filesize'),
					'max_post_size' => ini_get('post_max_size'),
				]) }}
			</x-upload>

			<x-buttons bottom align="right">
				<input class="button big cursor-pointer" type="submit" value="{{ ___('create') }}">
			</x-buttons>
			
			<input type="hidden" name="lang" value="fr">

		</form>

	</x-section>

</x-app-layout>
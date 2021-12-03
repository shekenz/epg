<x-app-layout>
	@php $escapedTitle = ___('edit variation').' "'.$book->label.'" '.__('from').' '.__('book').' "'.$book->bookInfo->title.'"'; @endphp
	<x-slot name='title'>
		{{ $escapedTitle }}
	</x-slot>

	<x-slot name="scripts">
		<script src="{{ asset('js/media-library-dragdrop.js') }}" type="text/javascript" defer></script>
		<script src="{{ asset('js/variations-create.js') }}" type="text/javascript" defer></script>
		<script src="{{ asset('js/variations-edit.js') }}" type="text/javascript" defer></script>
	</x-slot>


	{{-- //TODO return to book with $bookInfo->id --}}
	<x-section :title="$escapedTitle" :return="route('books')" class="full">

		@if ($errors->any())
			<x-warning>{{ __('app.errors.form') }}</x-warning>
		@endif

		@if($book->orders->isNotEmpty())
			<x-warning>{{ __('app.variations.warning') }}</x-warning>
		@endif

		<form id="edit-form" action="{{ route('variations.update', $book->id) }}" method="POST" enctype="multipart/form-data" autocomplete="off">
		@csrf
		@method('patch')

			<x-separator first>{{ ___('variation data') }}</x-separator>	

			<div class="flex flex-col md:grid md:grid-cols-4 md:gap-x-8">

				<x-input name="label" type="text" :label="___('label')" value="{{ old('label') ?? $book->label }}" maxlength="128">@error('label'){{$message}}@enderror</x-input>

				<x-input name="weight" type="number" :label="___('weight').' (g)'" value="{{ old('weight') ?? $book->weight }}" min="0" :disabled="($book->orders->isNotEmpty())">
					@error('weight'){{$message}}@enderror
				</x-input>

				@if($book->orders->isNotEmpty())
					<input name="weight" type="hidden" value="{{ $book->weight }}">
				@endif

				<div>
					<x-input name="stock" type="number" :label="___('stock')" value="{{ old('stock') ?? $book->stock }}" />
					<input id="stock-hidden" name="stock" type="hidden" disabled="true" value="{{ old('stock') ?? $book->stock }}">
					<x-checkbox name="pre-order" :checked="(old('pre_order') ?? $book->pre_order)" :label="___('pre-order')"/>
				</div>

				<div>
					<x-input name="price" type="number" :label="___('price')" value="{{ old('price') ?? $book->price }}" min="0" step="0.01" :disabled="($book->orders->isNotEmpty())">
						@error('price'){{$message}}@enderror
					</x-input>

					@if($book->orders->isNotEmpty())
						<input name="price" type="hidden" value="{{ $book->price }}">
					@endif
				</div>

			</div>
			
			{{-- Media Library --}}
			@if( $media->isNotEmpty() )
				<x-separator>{{ ___('media') }}</x-separator>

				<div class="flex gap-x-4">

					<x-media-dropzone id="media-link" label="{{ ___('linked media') }}">
						@if($book->media->isEmpty())
							<x-slot name="placeholder">{{ __('app.media.link-placeholder') }}</x-slot>
						@endif
						@foreach( $book->media as $medium )
							<x-media-item :src="asset('storage/'.$medium->preset('thumb'))" :src2x="asset('storage/'.$medium->preset('thumb2x'))" :medium-id="$medium->id" input/>
						@endforeach
					</x-media-dropzone>

					<x-arrows-helper />

					<x-media-dropzone id="media-library" label="{{ ___('media library') }}">
						@if($media->isNotEmpty() && $media->diff($book->media)->isEmpty())
							<x-slot name="placeholder">{{ __('app.media.library-placeholder') }}</x-slot>
						@endif
						@foreach($media->diff($book->media) as $medium)
							<x-media-item :src="asset('storage/'.$medium->preset('thumb'))" :src2x="asset('storage/'.$medium->preset('thumb2x'))" :medium-id="$medium->id" />
						@endforeach
					</x-media-dropzone>

				</div>
			@endif

			{{-- Upload Media --}}
			<x-upload :label="__('Upload and link new media')" label-class="mt-3">
				{{ __('app.upload.limits', [
					'max_files' => ini_get('max_file_uploads'),
					'max_file_size' => ini_get('upload_max_filesize'),
					'max_post_size' => ini_get('post_max_size'),
				]) }}
			</x-upload>

			<x-buttons bottom align="right">
				<input class="button big" type="submit" value="{{ ___('save') }}">
			</x-buttons>
	
		</form>

	</x-section>
	
</x-app-layout>
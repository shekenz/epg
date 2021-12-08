<x-app-layout>

	@php $escapedTitle = ___('add variation to book').' "'.$bookInfo->title.'"'; @endphp
	<x-slot name='title'>
		{{ $escapedTitle }}
	</x-slot>

	<x-slot name="scripts">
		<script src="{{ asset('js/media-library-dragdrop.js') }}" type="text/javascript" defer></script>
		<script src="{{ asset('js/variations-create.js') }}" type="text/javascript" defer></script>
	</x-slot>

	<x-section :title="$escapedTitle" :return="url()->previous()" class="full">

		@if ($errors->any())
			<x-warning>{{ __('app.errors.form') }}</x-warning>
		@endif

		<form action="{{ route('variations.store', $bookInfo->id) }}" method="POST" enctype="multipart/form-data" autocomplete="off">
		@csrf

			<x-separator first>{{ ___('variation data') }}</x-separator>	

			<div class="flex flex-col md:grid md:grid-cols-4 md:gap-x-8">

				<x-input name="label" type="text" :label="___('label')" value="{{ old('label') }}" maxlength="128">@error('label'){{$message}}@enderror</x-input>
				<x-input name="weight" type="number" :label="___('weight').' (g)'" value="{{ old('weight') }}" min="0">@error('weight'){{$message}}@enderror</x-input>

				<div>
					<x-input name="stock" type="number" :label="___('stock')" value="{{ old('stock') }}" min="0">@error('stock'){{$message}}@enderror</x-input>
					<x-checkbox name="pre-order" :checked="(old('pre_order'))" :label="___('pre-order')"/>
				</div>

				<x-input name="price" type="number" :label="___('price')" value="{{ old('price') }}" min="0" step="0.01">@error('price'){{$message}}@enderror</x-input>

			</div>
				
			@if( $media->isNotEmpty() )
				<x-separator>{{ ___('media') }}</x-separator>

				<div class="flex gap-x-4">

					<x-drop-zone id="media-link" label="{{ ___('linked media') }}">
						<x-slot name="placeholder">{{ __('app.media.link-placeholder') }}</x-slot>
					</x-drop-zone>

					<x-arrows-helper />

					<x-drop-zone id="media-library" label="{{ ___('media library') }}">
						<x-slot name="placeholder">{{ __('app.media.library-placeholder') }}</x-slot>
						@foreach($media as $medium)
							<x-drop-item :src="asset('storage/'.$medium->preset('thumb'))" :src2x="asset('storage/'.$medium->preset('thumb2x'))" :medium-id="$medium->id" />
						@endforeach
					</x-drop-zone>

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
				<input class="button big cursor-pointer" type="submit" value="{{ ___('add') }}">
			</x-buttons>

		</form>
	
	</x-section>
	
</x-app-layout>
<x-app-layout>
	<x-slot name="title">
		{{ ___('media') }}
	</x-slot>

	<x-section :title="___('media')" class="full">
		<x-buttons>
			<x-button :href="route('media.create')" class="big" :label="___('upload')" />
		</x-buttons>
		<div class="
			grid
			grid-cols-3
			md:grid-cols-4
			lg:grid-cols-6
			xl:grid-cols-8
			gap-1
			md:gap-2
			lg:gap-4
		">
		@foreach($media as $medium)
			<x-media-item :src="asset('storage/'.$medium->preset('thumb'))" :src2x="asset('storage/'.$medium->preset('thumb2x'))" :medium="$medium"/>
		@endforeach
		</div>
	</x-section>

</x-app-layout>
<x-index-layout>

	<x-slot name="title">About</x-slot>

	<div class="
		mx-4
		md:grid
		md:grid-cols-9
		md:mx-0
	">
		<div class="
			about
			slider
			md:col-span-4
			xl:col-start-2
			xl:col-span-3
			xl:mr-12
		">
			@php echo(preg_replace('/\[epg\]/', $acronyms[array_rand($acronyms)], $abouts[0])) @endphp
		</div>
		<div class="
			about
			md:col-start-6
			md:col-span-4
			xl:col-start-6
			xl:col-span-3
		">
			@php echo(preg_replace('/\[epg\]/', $acronyms[array_rand($acronyms)], $abouts[1])) @endphp
		</div>
		{{-- <div class="col-span-9 xl:col-start-2 xl:col-span-7 my-8"><a href="#" class="base-link">terms and condition</a></div> --}}
	</div>
</x-index-layout>
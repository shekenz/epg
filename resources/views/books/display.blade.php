<x-app-layout>
	<x-slot name="title">
			{{ ___('book').' : '.$bookInfo->title }}
	</x-slot>

	

	<x-section :title="___('book').' : '.$bookInfo->title" :return="route('books')">

		<x-buttons>
			<x-button :href="route('books.edit', $bookInfo->id )" :label="___('edit')" class="big" />
		</x-buttons>

		<div class="flex gap-x-8">

			<div class="w-1/3 flex-shrink-0">

				<x-separator first>{{ ___('general informations') }}</x-separator>

				@env('local')
				<span class="font-bold">{{ __('ID') }} : </span>{{ $bookInfo->id }}<br>
				@endenv
				<span class="font-bold">{{ ___('title') }} : </span>{{ $bookInfo->title }}<br>
				<span class="font-bold">{{ ___('author') }} : </span>{{ $bookInfo->author }}<br>
				<span class="font-bold">{{ ___('width') }} : </span>
				@if( !empty($bookInfo->width))
					{{ $bookInfo->width }} mm
				@else
					{{ ___('empty') }}
				@endif
				<br>
				<span class="font-bold">{{ ___('height') }} : </span>
				@if( !empty($bookInfo->height))
					{{ $bookInfo->height }} mm
				@else
					{{ ___('empty') }}
				@endif
				<br>
				<span class="font-bold">{{ ___('pages count') }} : </span>
				@if( !empty($bookInfo->pages))
					{{ $bookInfo->pages }} pages
				@else
					{{ ___('empty') }}
				@endif
				<br>
				<span class="font-bold">{{ ___('cover') }} : </span>
				@if( !empty($bookInfo->cover))
					{{ $bookInfo->cover }}
				@else
					{{ ___('empty') }}
				@endif
				<br>
				<span class="font-bold">{{ ___('copies') }} : </span>
				@if( !empty($bookInfo->width))
					{{ $bookInfo->copies }}
				@else
					{{ ___('empty') }}
				@endif
				<br>
				<span class="font-bold">{{ ___('year') }} : </span>
				@if( !empty($bookInfo->year))
					{{ $bookInfo->year }}
				@else
					{{ ___('empty') }}
				@endif
				<br>
				<span class="font-bold">{{ ___('published by') }} : </span>{{ $bookInfo->user->username }}<br>
			</div>

			<div class="">
				<x-separator first>{{ ___('description') }}</x-separator>
				<p class="mb-4">{!! nl2br(e($bookInfo->description)) !!}</p>
			</div>

		</div>

		<div class="col-span-4">
			<x-separator>{{ ___('variations') }}</x-separator>
			<table class="big">
				<thead>
					<tr>
						@if( config('app.env') == 'local')
						<td>{{ ___('id') }}</td>
						@endif
						<td>{{ ___('label') }}</td>
						<td>{{ ___('weight') }}</td>
						<td>{{ ___('stock') }}</td>
						<td>{{ ___('pre order') }}</td>
						<td>{{ ___('price') }}</td>
					</tr>
				</thead>
				<tbody>
				@foreach ($bookInfo->books as $book)
					<tr>
						@env('local')
						<td>{{ $book->id }}</td>
						@endenv
						<td>{{ $book->label }}</td>
						<td>{{ $book->weight }} g</td>
						<td>{{ $book->stock }}</td>
						<td>{{ ___b($book->pre_order) }}</td>
						<td>{{ $book->price }} €</td>
				@endforeach
				</tbody>
			</table>
		</div>

	</x-section>
	    
</x-app-layout>
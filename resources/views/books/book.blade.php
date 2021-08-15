<article id="{{ Str::slug($book->title, '-') }}" class="grid grid-cols-9">
	<div class="
		carousel
		col-span-9
		mr-0
		xl:col-span-7
		xl:mr-12
	" data-slick='{"slidesToShow": 1, "slidesToScroll": 4}'>
		<div class="glide">
			<div data-glide-el="track" class="glide__track">
				<ul class="glide__slides">
					@foreach ($book->media as $medium)
						<li class="glide__slide text-center"><img class="m-auto w-full" src="{{ asset('storage/'.$medium->preset('hd')) }}"></li>
					@endforeach
				</ul>
			</div>
			<div class="glide__arrows hidden xl:block" data-glide-el="controls">
				<button class="glide__arrow2 glide__arrow2--left" data-glide-dir="<"></button>
				<button class="glide__arrow2 glide__arrow2--right" data-glide-dir=">"></button>
			</div>
			<div class="glide__bullets xl:hidden" data-glide-el="controls[nav]">
				@if(count($book->media) != 1)
					@foreach ($book->media as $medium)
						<button class="glide__bullet" data-glide-dir="={{ $medium->order }}"></button>
					@endforeach
				@endif
			</div>
					
		</div>
		<div id="counter-{{ $glideIndex }}" class="hidden xl:block xl:mt-8 xl:mb-12"><span class="counter-index">1</span>/<span class="counter-total">{{ $book->media->count() }}</span></div>
	</div>
	<div class="
		info
		grid
		grid-cols-3
		col-span-9
		pb-16
		pt-4
		xl:pt-0
		xl:block
		xl:col-start-8
		xl:col-span-2
	">
		<div class="mr-4 xl:mr-0">
				{!! $book->title !!}<br> {{-- TODO Fix that hack (to allow line break in title) --}}
				@if( !empty($book->author) )
				{{ $book->author }}<br>
				@endif
				@if( !(empty($book->height) && empty($book->width) && empty($book->cover) && empty($book->pages) && empty($book->year)) )
				<br>
				@endif
				@if( !(empty($book->height) || empty($book->width)) )
				{{ $book->height }}mm x {{ $book->width }}mm<br>
				@endif
				@if ( !empty($book->cover) )
				{{ $book->cover }}<br>
				@endif
				@if( !empty($book->pages) )
					{{ $book->pages }} pages<br>
				@endif
				@isset($book->copies)
					{{ $book->copies.' '.__('copies') }}<br>
				@endif
				@if( !empty($book->year) )
					{{ $book->year }}<br>
				@endif

				@if( !empty($book->price) && setting('app.shop.enabled'))
					<br>{{ $book->price }} €<br>
					@if( $book->quantity > 0)
						<br>
						<a href="{{ route('cart.api.add', $book->id)}}" class="add-to-cart-button button-lg">{{ __('Add to cart') }}</a><br>
					@elseif( $book->pre_order)
						<br>
						<a href="{{ route('cart.api.add', $book->id)}}" class="add-to-cart-button button-lg">{{ __('Pre-order') }}</a><br>
					@else
						<br>
						({{ __('Out of stock') }})<br>
					@endif
				@endif
				<br>
				@auth
					<div class="hideable mb-4"><a href="{{ route('books.edit', $book->id) }}" class="user-edit">{{ __('Edit') }}</a><a href="{{ route('books.archive', $book->id) }}" class="user-edit">{{ __('Archive') }}</a></div>
				@endauth
		</div>
		<div class="col-span-2">
			<p class="mb-6 mr-6">
				{!! nl2br(e($book->description)) !!}
			</p>
		</div>
	</div>
</article>
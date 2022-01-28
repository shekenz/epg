<article id="{{ Str::slug($bookInfo->title, '-') }}" class="grid grid-cols-9">
	<div class="
		carousel
		col-span-9
		mr-0
		xl:col-span-7
		xl:mr-12
	">
		<div class="glide" id="glide-{{ $glideIndex }}">
			<div data-glide-el="track" class="glide__track">
				<ul class="glide__slides">
					@php $mediaIDs = []; @endphp
					@foreach ($bookInfo->books->first()->media as $index => $medium)
						<li class="glide__slide text-center"><img class="m-auto w-full" src="{{ asset('storage/'.$medium->preset('hd')) }}"></li>
						@php
								// Set media's position in the glide as 'media_id' => 'position_in_glide'
								$mediaIDs[$medium->id] = $index;
						@endphp
					@endforeach
				</ul>
			</div>
			<div class="glide__arrows hidden xl:block" data-glide-el="controls">
				<button class="glide__arrow2 glide__arrow2--left" data-glide-dir="<"></button>
				<button class="glide__arrow2 glide__arrow2--right" data-glide-dir=">"></button>
			</div>
			<div class="glide__bullets xl:hidden" data-glide-el="controls[nav]">
				@if(count($bookInfo->books->first()->media) != 1)
					@foreach ($bookInfo->books->first()->media as $medium)
						<button class="glide__bullet" data-glide-dir="={{ $medium->order }}"></button>
					@endforeach
				@endif
			</div>
					
		</div>
		<div id="counter-{{ $glideIndex }}" class="hidden xl:block xl:mt-8 xl:mb-12"><span class="counter-index">1</span>/<span class="counter-total">{{ $bookInfo->books->first()->media->count() }}</span></div>
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
				{{ $bookInfo->title }}<br>
				@if( !empty($bookInfo->author) )
				{{ $bookInfo->author }}<br>
				@endif
				@if( !(empty($bookInfo->height) && empty($bookInfo->width) && empty($bookInfo->cover) && empty($bookInfo->pages) && empty($bookInfo->year)) )
				<br>
				@endif
				@if( !(empty($bookInfo->height) || empty($bookInfo->width)) )
				{{ $bookInfo->height }}mm x {{ $bookInfo->width }}mm<br>
				@endif
				@if ( !empty($bookInfo->cover) )
				{{ $bookInfo->cover }}<br>
				@endif
				@if( !empty($bookInfo->pages) )
					{{ $bookInfo->pages }} pages<br>
				@endif
				@isset($bookInfo->copies)
					{{ $bookInfo->copies.' '.__('copies') }}<br>
				@endif
				@if( !empty($bookInfo->year) )
					{{ $bookInfo->year }}<br>
				@endif

				@if($bookInfo->books->count() > 1)
					<br>
					<div class="flex items-center">
						<form class="variations-form" id="variations-form-{{ $glideIndex }}">
							<select class="variations-select" data-glide-index="{{ $glideIndex }}">
							@foreach($bookInfo->books as $variation)
								@if($variation->media->isNotEmpty())
									{{-- //TODO get those data threw API --}}
									@php
										$variationData = [
											'id' => $variation->id,
											'media' => $variation->media->map(function($item) { return 'storage/'.$item->preset('hd'); }),
											'price' => $variation->price,
											'preorder' => $variation->pre_order,
											'stock' => $variation->stock,
											'extra' => $variation->extra
										];
									@endphp
									<option value="{{ json_encode($variationData) }}">{{ $variation->label }}</option>
								@endif
							@endforeach
							</select>
						</form>
						<img id="variations-loader-{{ $glideIndex }}" src="{{ asset('img/tiny_loader.svg') }}" class="hidden ml-2 w-8 h-8 inline-block">
					</div>
				@endif

				@if( !empty($bookInfo->books->first()->price) && setting('app.shop.enabled'))
					<br><span id="price-{{ $glideIndex }}">{{ $bookInfo->books->first()->price }}</span> €<br>
					
					<br>
					<a id="add-to-cart-{{ $glideIndex }}" href="{{ route('cart.api.add', $bookInfo->books->first()->id)}}" class="add-to-cart-button button-lg @if($bookInfo->books->first()->stock <= 0 && !$bookInfo->books->first()->pre_order) {{ 'out' }} @endif">
						<span data-label-add="{{ ___('add to cart') }}" data-label-pre="{{ ___('pre-order') }}" data-label-out="{{ ___('out of stock') }}">
							@if( $bookInfo->books->first()->stock > 0)
								{{ ___('add to cart') }}
							@elseif( $bookInfo->books->first()->pre_order)
								{{ ___('pre-order') }}
							@else
								{{ ___('out of stock') }}
							@endif
						</span>
					</a><br>

					<div class="@if(empty($bookInfo->books->first()->extra)) {{ 'hidden' }} @endif" id="extra-info-{{ $glideIndex }}"><br><span>{{ $bookInfo->books->first()->extra }}</span><br></div>

				@endif
				<br>
				@auth
					<div class="hideable mb-4">
						<a href="{{ route('books.edit', $bookInfo->id) }}" class="user-edit">{{ ___('edit') }}</a>
						{{-- <a href="{{ route('books.archive', $bookInfo->id) }}" class="user-edit">{{ ___('archive') }}</a> --}}
					</div>
				@endauth
		</div>
		<div class="col-span-2">
			<p class="mb-6 mr-6">
				{!! nl2br(e($bookInfo->description)) !!}
			</p>
		</div>
	</div>
</article>
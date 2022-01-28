<div id="article-{{ $article->id }}">
	<div class="relative">
		<a href="{{ route('cart.api.removeAll', $article->id) }}" class="remove-all-button square-button absolute right-2 top-2" title="{{ ___('remove article') }}">
			<svg viewbox="0 0 100 100">
				<line stroke-width="6" x1="33" x2="67" y1="33" y2="67" />
				<line stroke-width="6" x1="67" x2="33" y1="33" y2="67" />
			</svg>
		</a>
		<img class="mb-4 w-full" src="{{ asset('storage/'.$article->media->get(0)->preset('md')) }}" alt="test thumbnail">
	</div>
	<p>
		{{ $article->bookInfo->title }}@if($article->bookInfo->books->count() > 1){{ ' - '.$article->label }}@endif<br>
		@if(isset($article->bookInfo->author))
			{{ $article->bookInfo->author }}
		@else
			&nbsp;
		@endif
		<br><br>
		@if($article->pre_order)
		{{ ___('pre-order') }}<br>	
		@endif
		@if($article->extra)
		<span>{{ $article->extra }}</span><br>
		@endif
		@if($article->pre_order || $article->extra)
		<br>
		@endif
		{{ ___('quantity') }} : <span class="quantity-for-id-{{ $article->id }}">{{ $article->cartQuantity }}</span>
		<a class="qte-button square-button ml-2" href="{{ route('cart.api.add', $article->id) }}">
			<svg viewbox="0 0 100 100">
				<line stroke-width="6" x1="28" x2="72" y1="50" y2="50" />
				<line stroke-width="6" x1="50" x2="50" y1="28" y2="72" />
			</svg>
		</a>
		<a class="qte-button square-button ml-2" href="{{ route('cart.api.remove', $article->id) }}">
			<svg viewbox="0 0 100 100">
				<line stroke-width="6" x1="30" x2="70" y1="50" y2="50" />
			</svg>
		</a><br><br>
		{{ ___('subtotal') }} : <span class="subtotal-for-id-{{ $article->id }}">{{ $article->price * $article->cartQuantity }}</span>€
	</p>
</div>

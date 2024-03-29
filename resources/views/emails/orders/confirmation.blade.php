{{ __('mails.general.salutationto', ['name' => $order->given_name]) }} !
<br><br><br>
{{ __('mails.orders.confirmation.intro', ['order_id' => $order->order_id]) }}.<br><br>
{{ __('mails.orders.confirmation.summary') }} :
<br>----------------------------------------------------<br>

@php 
$total = 0;
$couponPrice = 0;
$shippingPrice = findStopPrice($order->total_weight, $order->shippingMethods->price, $order->shippingMethods->priceStops);
@endphp
@foreach ($order->books as $book)
	@php $total += $book->pivot->quantity * $book->price; @endphp
	{{ $book->bookInfo->title }}@if($book->bookInfo->books->count() > 1){{ ' - '.$book->label }}@endif X {{ $book->pivot->quantity }} : {{ round($book->pivot->quantity * $book->price, 2) }} € @if($book->pre_order)[{{ ___('pre-order') }}]@endif @if($book->extra)({{ $book->extra }})@endif @if(!$loop->last)<br>@endif
@endforeach
@isset($order->coupons)
<br>----------------------------------------------------<br>
@php
	if(boolval($order->coupons->type)) {
		$couponPrice =  $order->coupons->value * -1;
		$couponType = ' €'; 
	}else{
		$couponPrice = round($order->coupons->value / -100 * $total, 2);
		$couponType = '%'; 
	}
@endphp
{{ __('mails.orders.confirmation.coupon', [
	'coupon_value' => '-'.$order->coupons->value.$couponType,
	'coupon_price' => $couponPrice.' €'
]) }}
@endif
<br>----------------------------------------------------<br>
{{ __('mails.orders.confirmation.method', [
	'method' => $order->shippingMethods->label,
	'shipping_price' => $shippingPrice,
]) }} €
<br>----------------------------------------------------<br>
Total : {{ round($total + $shippingPrice + $couponPrice, 2) }} €
<br>----------------------------------------------------<br><br>

{{ __('mails.orders.confirmation.shipping') }}.<br><br><br>

{{ __('mails.general.contact') }} <a href="mailto:hello@epg.works">hello@epg.works</a>.<br><br>

{{ __('mails.orders.confirmation.thanks') }}
<br><br>
<a href="https://www.epg.works">https://www.epg.works</a>
<br><br><br>
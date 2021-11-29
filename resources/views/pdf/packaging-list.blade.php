<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Packaging list</title>

	<style>
		td {
			padding: 10px 15px;
			border:3px solid black;
		}

		h2 {
			font-size: 1.1em;
			margin-bottom: 30px;
			margin-top: 100px;
		}
		thead tr, tfoot tr {
			font-weight: bold;
		}
	</style>

{{-- </head> --}}
<body style="font-family:sans-serif">
	@foreach($orders as $order)
	<h1 style="border-bottom:3px solid black;padding-bottom:20px;margin-bottom:1cm">e.p.g.</h1>
	<div style="float:right;width:25%;border:3px solid black;padding:30px 50px;">
		<span style="display:block;font-weight:bold;margin-bottom:10px;">{{ $order->full_name }}</span>
		{{ $order->address_line_1 }},<br>
		@isset($order->address_line_2)
		{{ $order->address_line_2 }},<br>
		@endif
		{{ $order->postal_code }} {{ $order->admin_area_2 }}<br>@isset($order->admin_area_1) {{ $order->admin_area_1 }}<br>@endif
		{{ strtoupper(config('countries.'.$order->country_code))}}
	</div>
	<div style="width:25%;border:3px solid black;padding:30px 50px;">
		<span style="display:block;font-weight:bold;margin-bottom:10px;">e.p.g.</span>
		12 Cité Jandelle,<br>
		75019 Paris<br>
		FRANCE<br>
	</div>
	
	<div style="clear:both;">
		<h2 style="margin-top:2cm;margin-bottom:0.7cm" >{{ ___('packaging list') }}</h2>
		<div style="margin-bottom:0.3cm">{{ ___('order').' '}}<span style="font-weight:bold">{{ $order->order_id }}</span> {{ __('shipped on').' '.Carbon\Carbon::now()->locale(config('app.locale'))->isoFormat('L');  }}</div>
	</div>
	<table style="border-collapse: collapse;border:3px solid black;;width:100%;">
		@php $couponPrice = 0; @endphp
		<thead style="background-color:#ddd">
			<tr>
				<td>{{ ___('articles') }}</td>
				<td>{{ ___('quantity') }}</td>
				<td>{{ ___('unit price') }}</td>
				<td>{{ ___('subtotal') }}</td>
			</tr>
		</thead>
		<tbody>
			@php 
				$total = 0;
				$shippingPrice = findStopPrice($order->total_weight, $order->shippingMethods->price, $order->shippingMethods->priceStops);
			@endphp
			@foreach($order->books as $book)
			@php 
				$subTotal = round($book->pivot->quantity * $book->price, 2);
				$total += $subTotal;
			@endphp
			<tr>
				<td>{{ $book->bookInfo->title }}@if($book->bookInfo->books()->withTrashed()->count() > 1){{ ' - '.$book->label }}@endif</td>
				<td style="text-align:right;">{{ $book->pivot->quantity }}</td>
				<td style="text-align:right;">{{ $book->price }} €</td>
				<td style="text-align:right;">{{ $subTotal }} €</td>
			</tr>
			@endforeach
			@isset($order->coupons)
			<tr>
				@php $couponPrice = 
					boolval($order->coupons->type) ? 
					-1*$order->coupons->value : 
					round($order->coupons->value / -100 * $total, 2);
				@endphp
				<td>{{ ___('coupon').' : '.$order->coupons->label.' (-'.$order->coupons->value.(boolval($order->coupons->type) ? '€': '%').')' }}</td>
				<td colspan="3" style="text-align:right;">{{ $couponPrice.' €' }}</td>
			</tr>
			@endif
			<tr>
				<td>{{ ___('shipping').' : '.$order->shipping_method }}</td>

				<td colspan="3" style="text-align:right;">{{ $shippingPrice }} €</td>
			</tr>
		</tbody>
		<tfoot style="background-color:#ddd">
			<tr>
				<td>{{ ___('total') }}</td>
				<td colspan="3" style="text-align:right;">{{ round($total + $couponPrice + $shippingPrice, 2) }} €</td>
			</tr>
		</tfoot>
	</table>
	<div style="position:fixed;bottom:-200px;height:300px;text-align: center;margin-top:150px;border-top:3px solid black;padding-top:30px;">{{ __('Thanks for your purchase').' :)' }}</div>
	@if(!$loop->last)<div style="page-break-after: always;"></div>@endif
	@endforeach
</body>
</html>
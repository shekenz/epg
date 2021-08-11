{{ __('mails.general.salutationto', ['name' => $order->given_name ]) }}<br><br><br>

{{ __('mails.orders.shipped.intro', [
	'order_id' => $order->order_id,
	'shipped_date' => $order->shipped_at->toFormattedDateString(),
]) }}<br><br>

@isset($order->tracking_url)
{{ __('mails.orders.shipped.tracking') }} :<br>
<a href="{{ $order->tracking_url }}">{{ $order->tracking_url }}</a>
<br><br>
@endif
{{ __('mails.orders.shipped.reclamation') }}.
<br><br><br>
{{ __('mails.orders.confirmation.thanks') }}
<br><br>
<a href="https://www.epg.works">https://www.epg.works</a>
<br><br><br>
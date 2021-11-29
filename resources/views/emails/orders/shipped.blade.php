{{ __('mails.general.salutationto', ['name' => $order->given_name ]) }}<br><br><br>

{{ __('mails.orders.shipped.intro', [
	'order_id' => $order->order_id,
	'shipped_date' => $order->shipped_at->locale(config('app.locale'))->isoFormat('L'),
]) }}<br><br>

@isset($order->tracking_url)
{{ __('mails.orders.shipped.tracking') }} <a href="https://www.laposte.fr/outils/suivre-vos-envois">La Poste</a> :<br>
{{ $order->tracking_url }}
<br><br>
@endif
<br>
{{ __('mails.general.contact') }} <a href="mailto:hello@epg.works">hello@epg.works</a>.<br><br>
{{ __('mails.orders.confirmation.thanks') }}
<br><br>
<a href="https://www.epg.works">https://www.epg.works</a>
<br><br><br>
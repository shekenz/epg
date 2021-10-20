<?php

return [
	'orders' => [
		'confirmation' => [
			'subject' => 'Your e.p.g. order :order_id',
			'intro' => 'Your order n° :order_id have been confirmed',
			'summary' => 'Order summary',
			'shipping' => 'You will soon receive another email to inform you when your order has been shipped',
			'coupon' => 'Coupon discount (:coupon_value) : :coupon_price',
			'method' => 'Shipping fees : :shipping_price',
			'thanks' => 'Thank you for orderning on e.p.g.',

		],
		'shipped' => [
			'subject' => 'Your e.p.g order has been shipped !',
			'intro' => 'Your order n° :order_id has been shipped on :shipped_date. You should receive it soon.',
			'tracking' => 'Here is your tracking number on',
			'reclamation' => 'Please contact us if you encounter any problems or for any reclamations',
		],
		'notification' => [
			'subject' => 'New order on e.p.g.',
			'line1' => 'A new order has been registered.',
			'line2' => 'Check your dashboard to process the order.',
		],
	],
	'contact' => [
		'notification' => [
			'subject' => 'Your message has been received',
			'line1' => 'Your message has been transmited to our team',
			'line2' => 'Please consider a response time up to 2 working days',
			'line3' => 'You will find a sumary of your message after de line below'
		]
	],
	'users' => [
		'invite' => [
			'subject' => 'You are invited to join our team!',
			'main' => 'You have been invited to join the e.p.g. moderation team!',
			'link' => 'Click on the link below to register your new account',
			'warning' => 'Please note that this link is only available for 24h',
		]
	],
	'general' => [
		'salutation' => 'Hi',
		'salutationto' => 'Hi :name',
		'regards' => 'Regards',
		'signature' => 'e.p.g.',
		'contact' => 'For any questions, please contact us at',
	]
];
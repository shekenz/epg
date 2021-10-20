<?php

return [
	'orders' => [
		'confirmation' => [
			'subject' => 'Votre commande :order_id',
			'intro' => 'Votre commande n° :order_id est confirmée',
			'summary' => 'Récapitulatif de la commande',
			'shipping' => 'Vous recevrez bientôt un mail de confirmation lorsque le colis sera expédié',
			'coupon' => 'Réduction coupon (:coupon_value) : :coupon_price',
			'method' => 'Frais d\'envois : :shipping_price',
			'thanks' => 'Merci pour votre achat sur e.p.g.',

		],
		'shipped' => [
			'subject' => 'Votre commande e.p.g a été envoyée !',
			'intro' => 'Votre commande n° :order_id a été envoyé le :shipped_date. Vous la recevrez sous peu.',
			'tracking' => 'Voici votre numéro de suivit sur',
			'reclamation' => 'Veuillez nous contacter si vous rencontrez des problèmes, ou pour toute autre question',
		],
		'notification' => [
			'subject' => 'Nouvelle commande sur e.p.g.',
			'line1' => 'Une nouvelle commande a été effectuée.',
			'line2' => 'Veuillez vous connecter sur le dashboard pour gérer la commande.',
		],
	],
	'contact' => [
		'notification' => [
			'subject' => 'Votre message a bien été reçu',
			'line1' => 'Votre message bien a été transféré à notre équipe',
			'line2' => 'Veuillez s\'il vous plait tenir compte d\'un temps de réponse pouvant aller jusqu\'à deux jours',
			'line3' => 'Vous trouverez un résumé de votre message après la ligne'
		]
	],
	'users' => [
		'invite' => [
			'subject' => 'Vous êtes invité(e) à rejoindre notre équipe !',
			'main' => 'Vous avez été invité(e) à rejoindre l\'équipe de modération d\'e.p.g. !',
			'link' => 'Veuillez suivre le lien ci-dessous afin de procéder à votre inscription',
			'warning' => 'Attention, ce lien n\'est valable que 24h',
		]
	],
	'general' => [
		'salutation' => 'Bonjour',
		'salutationto' => 'Bonjour :name',
		'regards' => 'Cordialement',
		'signature' => 'e.p.g.',
		'contact' => 'Pour toutes questions, veuillez nous contacter via',
	]
];
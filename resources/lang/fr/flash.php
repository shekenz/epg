<?php

return [
	'mail' => [
		'success' => 'Votre message a été envoyé. Vous recevrez une confirmation par email sous peu.',
		'fail' => 'Votre message n\' pas pu être envoyé en raison d\'une erreur interne. Nous nous excusons pour la gène occasionée.',
	],
	'user' => [
		'invited' => 'Une nouvelle invitation a été envoyée à :email.',
		'expired' => 'Ce lien a expiré. Veuillez nous contacter pour en obtenir un nouveau.',
	],
	'book' => [
		'added' => 'Nouveau livre ajouté !',
		'updated' => 'Livre mis à jour.',
		'archived' => 'Livre archivé.',
		'restored' => 'Livre restauré.',
		'deleted' => 'Livre supprimé.',
		'still-linked' => 'Impossible de supprimer le livre car il est lié à une commande active. Archivez d\'abord la commande.',
		'some-still-linked' => 'Impossible de supprimer certain livres car ils sont toujours liés à une commande active. Archivez d\'abord la commande.',
		'all-deleted' => 'Tous les livres archivés ont été supprimés.',
	],
	'variations' => [
		'deleted' => 'Variation supprimée',
		'linked-warning' =>	'La variation a été déplacée dans la liste d\'attente parce qu\'elle est toujours liée à une ou plusieurs commandes actives. Veuillez d\'abord archiver ces commandes pour pouvoir complètement supprimer la variation.',
		'restored' => 'Variation restorée.',
	],
	'media' => [
		'added' => 'Nouveau(x) média(s) ajouté(s) !',
		'deleted' => 'Média supprimé.',
		'renamed' => 'Le média a été renomé.',
	],
	'settings' => [
		'updated' => 'Paramètres mis à jour.',
		'published' => 'Le site est maintenant publié.',
		'unpublished' => 'Le site n\'est plus accessible aux utilisateurs.',
		'shop' => [
			'enable' => 'Les fonctionalités de commerce sont maintenant activées.',
			'disable' => 'Les fonctionalités de commerce sont maintenant désactivées.',
			'error' => 'Impossible d\'activer les fonctionalités de commerce.',
			'reasons' => [
				'noShippingMethods' => 'Aucune méthode d\'envois enregistré. Créez-en une au minimum.',
				'noPaypalCredentials' => 'Identifiants Paypal manquant. Veuillez vérifier vos paramètres Paypal.',
			],
		],
	],
	'cart' => [
		'stockUpdated' => 'Votre panier a été mis à jour suite à la fluctuation de notre stock.',
		'stockLimit' => 'Impossible d\'ajouter plus d\'articles. Limite de stock atteinte.',
	],
	'paypal' => [
		'credentials' => 'Vous devez enregistrer vos identifiants Paypal dans les paramètres pour activer les fonctionalités de payment.',
		'sandbox' => 'Paypal est paramétré en mode sandbox.',
		'recycle' => 'Impossible de recycler. La transaction est toujours en attente.'
	],
];
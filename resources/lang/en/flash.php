<?php

return [
	'mail' => [
		'success' => 'Your message has been sent. You will receive a confirmation by mail soon.',
		'fail' => 'Your message could not be sent because of an internal error. We are sorry for the inconveniance.',
	],
	'user' => [
		'invited' => 'A new invitation has been sent to :email.',
		'expired' => 'This link has expired. Please contact us to request a new link.',
	],
	'book' => [
		'added' => 'New book successfully added!',
		'updated' => 'Book successfully updated!',
		'archived' => 'Book archived.',
		'restored' => 'Book restored.',
		'deleted' => 'Book deleted.',
		'still-linked' => 'Can\'t delete a book that is linked to an order. Archive the order first.',
		'some-still-linked' => 'Can\'t delete certain books that are linked to an order. Archive the order first.',
		'all-deleted' => 'All archived book have been deleted.',
	],
	'variation' => [
		'deleted' => 'Variation deleted'
	],
	'media' => [
		'added' => 'New media added!',
		'deleted' => 'Media deleted.',
		'renamed' => 'Media has been renamed.',
	],
	'settings' => [
		'updated' => 'Settings updated.',
		'published' => 'Website is now published.',
		'unpublished' => 'Website is no more accessible.',
		'shop' => [
			'enable' => 'Shop features are now enabled.',
			'disable' => 'Shop features are now disabled.',
			'error' => 'Can\'t enable shop features.',
			'reasons' => [
				'noShippingMethods' => 'No shipping method registered. Create at least one shipping method.',
				'noPaypalCredentials' => 'No valid Paypal credentials registered. Please check your Paypal settings.',
			],
		],
	],
	'cart' => [
		'stockUpdated' => 'Your cart has been updated according to our stock fluctuation.',
		'stockLimit' => 'Cannot add more articles. Stock limit has been reached.',
	],
	'paypal' => [
		'credentials' => 'You must set your Paypal credentials in Settings to activate payment functionality.',
		'sandbox' => 'Paypal is set on sandbox.',
		'recycle' => 'Cannot recycle. Transaction is still pending.',
	],
];
<?php

return [
	'img-popup-help' => 'Naviguez avec les touches fléchées, touche X pour fermer',
	'variations' => [
		'deleted-waiting-list' => 'Variations supprimées en attente de confirmation',
		'warning' => 'Cette variation est liée à une ou plusieurs commandes actives. Veuillez d\'abord archiver ces commandes avant de pouvoir éditer les données vérouillées.',
	],
	'confirmations' => [
		'delete-all-books' => 'Etes-vous sûr de vouloir définitivement supprimer tout les livres ? Cette action est irreversible.',
		'delete-book' => 'Etes-vous sûr de vouloir définitivement supprimer le livre ":book" ? Les variations seront également supprimées. Cette action est irreversible.',
		'delete-variation' => 'La variation sera supprimée de façon permanente à moins qu\'elle ne soit liée à une commande active. Etes-vous sûr de vouloir supprimer ":variation" ?',
		'refresh-variation' => 'Etes-vous spur de vouloir supprimer définitivement ":variation" ? Cette action est irreversible.',
		'delete-media' => 'Etes-vous spur de vouloir supprimer définitivement ":media" ? Cette action est irreversible.',
	],
	'media' => [
		'link-placeholder' => 'Déposez les média de la bibliothèque ici',
		'library-placeholder' => 'Déplacez les média ici pour les détacher du livre',
		'infos' => [
			'not-found' => 'Image source manquante',
			'not-resized' => 'Les dimensions de l\'image n\'ont pas été réduite (l\'originale est plus petite que la configuration)',
		]
	],
	'upload' => [
		'info' => 'Cliquez ici pour ajouter des fichiers',
		'limits' => ':max_files fichiers de :max_file_size chacun max, au format JPG, GIF, ou PNG, pour un total maximum de :max_post_size',
	],
	'errors' => [
		'form' => 'Votre formulaire contient des erreurs. Veuillez vérifier les champs avant de pouvoir continuer.',
	],
	'warnings' => [
		'missing-media' => 'La variation ne sera pas affiché si il n\'y a pas de média',
		'missing-books' => 'Aucune variation trouvé ! Le livre ne sera pas affiché sur la page principale',
	],
];
<?php

namespace App\Http\Services;

use InvalidArgumentException;
use App\Models\Client;

class NewsletterClient
{
	
	/**
	 * Creates a new Client.
	 * Silent validation : if client already exist, data is overwritten.
	 *
	 * @param  array $clientData
	 * @return void
	 */
	public static function create(array $clientData)
	{

		// Validating $clientData fields
		$validKeys = ['email', 'firstname', 'lastname', 'country_code'];

		if(count(array_intersect_key(array_flip($validKeys), $clientData)) !== count($validKeys))
		{
			throw new InvalidArgumentException('One or more fields from client data are missing');
		}

		// Checking for existing client
		$existingClient = Client::where('email', $clientData['email'])->first();

		// If client doesn't exist, create it or update previous data
		if($existingClient === null)
		{
			return Client::create($clientData);
		}
		else
		{
			$existingClient->firstname = $clientData['firstname'];
			$existingClient->lastname = $clientData['lastname'];
			$existingClient->country_code = $clientData['country_code'];
			$existingClient->save();
			return $existingClient;
		}
	}

}
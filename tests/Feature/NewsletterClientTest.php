<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Services\NewsletterClient;
use App\Models\Client;
use InvalidArgumentException;

class NewsletterClientTest extends TestCase
{

		/**
		 * Testing if NewsletterClient creates or updates a Client model.
		 *
		 * @return void
		 * @test
		 */
		public function testNewsletterClientCreation()
		{
			$createdClient = NewsletterClient::create([
				'email' => 'test@test.com',
				'firstname' => 'test',
				'lastname' => 'test',
				'country_code' => 'FR',
			]);

			$this->assertInstanceOf(Client::class, $createdClient);
		}
		
		/**
		 * Testing if NewsletterClient throw exception if missing data.
		 *
		 * @return void
		 * @test
		 */
		public function testNewsletterClientMissingData()
		{
			$this->expectException(InvalidArgumentException::class);
			NewsletterClient::create([]);
		}

}

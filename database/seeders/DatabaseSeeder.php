<?php

namespace Database\Seeders;

use App\Models\BookInfo;
use App\Models\Medium;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Seed the application's database.
	 *
	 * @return void
	 */
	public function run()
	{

		$media = Medium::first();

		if(isset($media)) {
			
			$this->call([
				BookInfoSeeder::class,
				BookSeeder::class,
			]);
	
			BookInfo::all()->each(function($bookInfo) {
				$books = $bookInfo->books;
				if($books->isEmpty()) {
					$bookInfo->delete();
				}
			});

		} else {

			$this->command->error('Media library is empty. Please upload media before seeding books');

		}


	}
}

<?php

namespace Database\Seeders;

use App\Models\BookInfo;
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

	}
}

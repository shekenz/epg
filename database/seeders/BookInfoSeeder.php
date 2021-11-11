<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BookInfo;
use Illuminate\Support\Facades\DB;

class BookInfoSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		// Emptying database first
		BookInfo::truncate();
		BookInfo::factory()->count(rand(4, 10))->create();
	}
}

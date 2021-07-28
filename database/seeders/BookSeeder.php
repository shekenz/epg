<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use Illuminate\Support\Facades\DB;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		// Emptying database first
		Book::truncate();
		DB::table('book_medium')->truncate();

        Book::factory()->count(rand(4, 10))->create();
    }
}

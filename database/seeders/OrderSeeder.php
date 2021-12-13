<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\ArchivedOrder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		ArchivedOrder::truncate();
		Order::truncate();
		DB::table('book_order')->truncate();
		Order::factory()->count(100)->create();
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DatabaseClean extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Empty books, orders, archived_orders and related pivot tables';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		if ($this->confirm('This will delete all data from books, orders, archived_orders and related pivot tables. Are you sure you want to proceed ?')) {
			DB::table('archived_orders')->truncate();
			DB::table('books')->truncate();
			DB::table('book_medium')->truncate();
			DB::table('book_order')->truncate();
			DB::table('orders')->truncate();
		}
		$this->info('Databas cleaned !');
        return 0;
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
			Schema::table('books', function (Blueprint $table) {
				$table->dropColumn('title');
				$table->dropColumn('author');
				$table->dropColumn('user_id');
				$table->dropColumn('description');
				$table->dropColumn('width');
				$table->dropColumn('height');
				$table->dropColumn('cover');
				$table->dropColumn('pages');
				$table->dropColumn('copies');
				$table->integer('quantity')->change();
				$table->renameColumn('quantity', 'stock');
				$table->dropColumn('year');
				$table->dropColumn('created_at');
				$table->dropColumn('updated_at');
				$table->after('id', function($table) {
					$table->unsignedBigInteger('book_info_id');
				});
			});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('books', function (Blueprint $table) {
				$table->after('id', function($table) {
					$table->string('title', 128);
					$table->string('author', 64)->nullable();
					$table->unsignedBigInteger('user_id');
					$table->text('description');
					$table->unsignedInteger('width')->nullable();
					$table->unsignedInteger('height')->nullable();
					$table->string('cover', 255)->nullable();
					$table->unsignedInteger('pages')->nullable();
					$table->integer('copies')->nullable();
				});
				$table->bigInteger('stock')->nullable()->change();
				$table->renameColumn('stock', 'quantity');
				$table->after('pre_order', function($table) {
					$table->unsignedSmallInteger('year')->nullable();
					$table->timestamp('created_at')->nullable();
					$table->timestamp('updated_at')->nullable();
				});
				$table->dropColumn('book_info_id');
			});
    }
}

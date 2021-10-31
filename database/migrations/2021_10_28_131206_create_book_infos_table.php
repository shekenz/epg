<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_infos', function (Blueprint $table) {
            $table->id();
						$table->string('title', 128);
						$table->string('author', 64)->nullable();
						$table->text('description')->nullable();
						$table->unsignedSmallInteger('width')->nullable();
						$table->unsignedSmallInteger('height')->nullable();
						$table->unsignedSmallInteger('pages')->nullable();
						$table->string('cover', 64)->nullable();
						$table->unsignedInteger('copies')->nullable();
						$table->unsignedSmallInteger('year')->nullable();
						$table->unsignedInteger('position')->default(0);
						$table->unsignedBigInteger('user_id');
						$table->timestamp('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book_infos');
    }
}

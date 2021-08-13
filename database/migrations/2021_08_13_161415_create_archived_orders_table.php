<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchivedOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archived_orders', function (Blueprint $table) {
            $table->bigInteger('id', false, true)->unique();
			$table->string('order_id', 17);
			$table->string('transaction_id', 17);
			$table->string('payer_id', 13);
			$table->string('surname', 140);
			$table->string('given_name', 140);
			$table->string('full_name', 300);
			$table->string('phone_number', 14)->nullable();
			$table->string('email_address', 254);
			$table->string('address_line_1', 300);
			$table->string('address_line_2', 300)->nullable();
			$table->string('admin_area_2', 120);
			$table->string('admin_area_1', 300)->nullable();
			$table->string('postal_code', 60);
			$table->string('country_code', 2);
			$table->text('books_data');
			$table->tinytext('coupon_data')->nullable();
			$table->tinytext('shipping_data');
			$table->integer('total_weight', false, true);
			$table->timestamp('shipped_at');
			$table->string('tracking_url', 255)->nullable();
			$table->string('status', 255);
			$table->boolean('pre_order')->default(false);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('archived_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('archived_orders');
    }
}

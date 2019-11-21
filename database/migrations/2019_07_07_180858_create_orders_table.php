<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('product_id')->nullable();
            $table->decimal('cost', 16, 2)->nullable();
            $table->decimal('price', 16, 2)->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('subtotal', 16, 2)->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('serial_no')->nullable();
            $table->integer('orderable_id')->nullable();
            $table->string('orderable_type')->nullable();
            $table->integer('pre_order_item_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}

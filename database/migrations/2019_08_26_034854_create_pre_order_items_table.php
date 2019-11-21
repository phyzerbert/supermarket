<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePreOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pre_order_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('pre_order_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->integer('cost')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('discount')->nullable();
            $table->string('discount_string')->nullable();
            $table->integer('subtotal')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('serial_no')->nullable();
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
        Schema::dropIfExists('pre_order_items');
    }
}

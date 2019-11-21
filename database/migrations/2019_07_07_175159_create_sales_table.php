<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer("user_id")->nullable();
            $table->dateTime('timestamp')->nullable();
            $table->string('reference_no')->nullable();
            $table->integer('store_id')->nullable();
            $table->integer('company_id')->nullable();
            $table->integer('biller_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->string('attachment')->nullable();
            $table->text('note')->nullable();
            $table->integer('status')->nullable();
            $table->integer('discount')->default(0);
            $table->string('discount_string')->nullable();
            $table->integer('shipping')->default(0);
            $table->string('shipping_string')->nullable();
            $table->integer('grand_total')->default(0);
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
        Schema::dropIfExists('sales');
    }
}

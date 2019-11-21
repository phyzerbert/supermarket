<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePreOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pre_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer("user_id")->nullable();
            $table->dateTime('timestamp')->nullable();
            $table->string('reference_no')->nullable();
            $table->integer('company_id')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->integer('discount')->default(0);
            $table->string('discount_string')->nullable();
            $table->integer('grand_total')->default(0);
            $table->integer('credit_days')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('attachment')->nullable();
            $table->text('note')->nullable();
            $table->integer('status')->nullable();
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
        Schema::dropIfExists('pre_orders');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer("user_id")->nullable();
            $table->dateTime('timestamp')->nullable();
            $table->string('reference_no')->nullable();
            $table->integer('store_id')->nullable();
            $table->integer('company_id')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->decimal('discount', 16, 2)->default(0);
            $table->string('discount_string')->nullable();
            $table->decimal('shipping', 16, 2)->default(0);
            $table->string('shipping_string')->nullable();
            $table->decimal('returns', 16, 2)->default(0);
            $table->decimal('grand_total', 16, 2)->default(0);
            $table->integer('credit_days')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('attachment')->nullable();
            $table->text('note')->nullable();
            $table->integer('status')->default(0);
            $table->integer('order_id')->nullable();
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
        Schema::dropIfExists('purchases');
    }
}

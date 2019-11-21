<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('timestamp')->nullable();
            $table->string('reference_no')->nullable();
            $table->decimal('amount', 16, 2)->nullable();
            $table->string('attachment')->nullable();
            $table->text('note')->nullable();
            $table->integer('status')->default(0);
            $table->integer('paymentable_id')->nullable();
            $table->string('paymentable_type')->nullable();
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
        Schema::dropIfExists('payments');
    }
}

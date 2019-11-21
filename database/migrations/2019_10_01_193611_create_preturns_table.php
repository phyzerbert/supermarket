<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePreturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preturns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('timestamp')->nullable();
            $table->string('reference_no')->nullable();
            $table->integer('amount')->nullable();
            $table->string('attachment')->nullable();
            $table->text('note')->nullable();
            $table->integer('status')->default(0);
            $table->integer('purchase_id')->nullable();
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
        Schema::dropIfExists('preturns');
    }
}

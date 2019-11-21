<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('code')->nullable();
            $table->integer('barcode_symbology_id')->nullable();
            $table->integer('category_id')->nullable();
            $table->string('unit')->nullable();
            $table->integer('currency_id')->nullable();
            $table->decimal('cost', 16, 2)->nullable();
            $table->decimal('price', 16, 2)->nullable();
            $table->integer('tax_id')->nullable();
            $table->integer('tax_method')->nullable();
            $table->integer('alert_quantity')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->string('image')->nullable();
            $table->text('detail')->nullable();
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
        Schema::dropIfExists('products');
    }
}

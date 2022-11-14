<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_rates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id');
            $table->string('name');
            $table->longText('description')->nullable();
            $table->float('purchase_price')->nullable();
            $table->float('sales_price')->nullable();
            $table->string('purchase_margin')->nullable();
            $table->string('sales_margin')->nullable();
            $table->float('discount')->nullable();
            $table->float('special_price')->nullable();
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
        Schema::dropIfExists('product_rates');
    }
}
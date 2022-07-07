<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierSpecialPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_special_prices', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_id');
            $table->string('product_id')->nullable();
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
        Schema::dropIfExists('supplier_special_prices');
    }
}

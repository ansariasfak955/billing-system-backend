<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rates', function (Blueprint $table) {
            $table->float('purchase_price')->nullable();
            $table->float('sales_price')->nullable();
            $table->string('purchase_margin')->nullable();
            $table->string('sales_margin')->nullable();
            $table->float('discount')->nullable();
            $table->float('special_price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rates', function (Blueprint $table) {
            //
        });
    }
}

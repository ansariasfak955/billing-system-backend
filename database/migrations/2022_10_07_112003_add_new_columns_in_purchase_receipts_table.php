<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsInPurchaseReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_receipts', function (Blueprint $table) {
            $table->string('amount')->nullable();
            $table->dateTime('expiration_date')->nullable();
            $table->enum('paid', ['0', '1'])->default(0);
            $table->string('paid_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_receipts', function (Blueprint $table) {
            //
        });
    }
}

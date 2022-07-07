<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable();
            $table->string('reference_number')->nullable();
            $table->dateTime('date')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->integer('category_id')->nullable();
            $table->float('amount')->nullable();
            $table->string('description')->nullable();
            $table->string('payment_option')->nullable();
            $table->string('employee')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('status')->nullable();
            $table->string('paid_by')->nullable();
            $table->dateTime('payment_date')->nullable();
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
        Schema::dropIfExists('purchase_tickets');
    }
}

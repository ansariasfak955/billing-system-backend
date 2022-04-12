<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesEstimatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_estimates', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable();
            $table->string('date')->nullable();
            $table->integer('client_id')->nullable();
            $table->string('status')->nullable();
            $table->string('payment_option')->nullable();
            $table->integer('created_by')->nullable();
            $table->string('title')->nullable();
            $table->string('agent_id')->nullable();
            $table->string('rate')->nullable();
            $table->integer('subject_to_vat')->default(0);
            $table->integer('subject_to_income_tax')->default(0);

            /* More Information Fields*/
            $table->string('inv_address')->nullable();
            $table->string('delivery_address')->nullable();
            $table->string('email_sent_date')->nullable();
            $table->string('valid_until')->nullable();
            $table->string('currency')->nullable();
            $table->float('currency_rate')->nullable();
            $table->string('comments')->nullable();
            $table->string('private_comments')->nullable();
            $table->string('addendum')->nullable();

            /* Signature Fields*/
            $table->string('name')->nullable();
            $table->string('tin')->nullable();
            $table->string('signature')->nullable();

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
        Schema::dropIfExists('sales_estimates');
    }
}

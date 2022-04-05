<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable();
            $table->string('legal_name')->nullable();
            $table->string('tin')->nullable();
            $table->string('phone_1')->nullable();
            $table->string('address')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('city')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('fax')->nullable();
            $table->string('website')->nullable();
            $table->string('comments')->nullable();
            $table->string('popup_notice')->nullable();
            $table->string('created_from')->default('web');
            $table->string('phone_2')->nullable();
            $table->string('payment_date')->nullable();
            $table->string('discount')->nullable();
            $table->string('rate')->nullable();
            $table->string('currency')->nullable();
            $table->string('subject_to_vat')->nullable();
            $table->string('maximum_risk')->nullable();
            $table->string('bank_account_format')->nullable();
            $table->string('bank_account_account')->nullable();
            $table->string('bank_account_bic')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}

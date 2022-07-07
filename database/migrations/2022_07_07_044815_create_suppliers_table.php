<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('legal_name')->nullable();
            $table->string('name')->nullable();
            $table->string('tin')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_1')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('country')->nullable();
            $table->string('address_latitude')->nullable();
            $table->string('address_longitude')->nullable();

            /* general fields */
            $table->string('fax')->nullable();
            $table->string('phone_2')->nullable();
            $table->string('website')->nullable();
            $table->integer('supplier_category')->default(0);
            $table->string('comments')->nullable();
            $table->string('popup_notice')->nullable();
            $table->string('created_from')->default('web');

            /* commercial fields */
            $table->string('payment_option_id')->nullable();
            $table->string('payment_terms_id')->nullable();
            $table->string('payment_date')->nullable();
            $table->string('payment_adjustment')->default("unspecified");
            $table->float('discount')->nullable();
            $table->integer('agent')->default(0);
            $table->string('rate')->nullable();
            $table->string('currency')->nullable();
            $table->enum('subject_to_vat', ['0', '1'])->default(0);
            $table->float('maximum_risk')->nullable();
            $table->integer('invoice_to')->default(0);
            $table->enum('subject_to_income_tax', ['0', '1'])->default(0);

            /* bank account fields */
            $table->string('bank_account_format')->nullable();
            $table->string('bank_account_account')->nullable();
            $table->string('bank_account_bic')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_description')->nullable();
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
        Schema::dropIfExists('suppliers');
    }
}

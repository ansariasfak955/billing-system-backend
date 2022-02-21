<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('commercial_name')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->string('pincode')->nullable();
            $table->string('phone')->nullable();
            $table->string('activity_type')->nullable();
            $table->string('email')->nullable();
            $table->longText('legal_registration')->nullable();
            $table->string('corporate_color')->nullable();
            $table->string('register_as')->nullable();
            $table->string('tin')->nullable();
            $table->string('fax')->nullable();
            $table->string('number_of_employees')->nullable();
            $table->string('website')->nullable();
            $table->string('language')->nullable();
            $table->string('time_zone')->nullable();
            $table->integer('fiscal_start_date')->default(1);
            $table->integer('fiscal_start_month')->default(1);
            $table->integer('number_of_decimal')->default(0);
            $table->string('decimal_separator')->default('.');
            $table->string('pdf_file_download_date_format')->default('dd/mm/yyyy');
            $table->string('currency')->default('EUR');
            $table->string('currency_representation')->default('symbol');
            $table->string('taxpayer_identification')->nullable();
            $table->string('logo')->nullable();
            $table->integer('user_id');
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
        Schema::dropIfExists('companies');
    }
}

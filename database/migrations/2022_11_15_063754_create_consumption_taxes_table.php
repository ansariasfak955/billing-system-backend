<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsumptionTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consumption_taxes', function (Blueprint $table) {
            $table->id();
            $table->string('primary_name');
            $table->enum('by_default_in_sales', ['0', '1'])->default('1');
            $table->enum('by_default_in_purchases', ['0', '1'])->default('1');
            $table->string('secondary_name')->nullable();
            $table->enum('activate_secondary_tax', ['0', '1'])->default('0');
            $table->enum('secondary_by_default_in_sales', ['0', '1'])->default('0');
            $table->enum('secondary_by_default_in_purchases', ['0', '1'])->default('0');
            $table->enum('subtractive', ['0', '1'])->default('0');
            $table->longText('taxes')->nullable();
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
        Schema::dropIfExists('consumption_taxes');
    }
}

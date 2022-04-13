<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('reference');
            $table->string('reference_id');
            $table->string('name')->nullable();
            $table->longText('description')->nullable();
            $table->float('base_price')->nullable();
            $table->string('quantity')->nullable();
            $table->float('discount')->nullable();
            $table->string('tax')->nullable();
            $table->string('income_tax')->nullable();
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
        Schema::dropIfExists('items');
    }
}

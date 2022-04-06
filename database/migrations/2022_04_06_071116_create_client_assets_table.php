<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_assets', function (Blueprint $table) {
            $table->id();
            $table->string('client_id')->nullable();
            $table->string('address')->nullable();
            $table->string('name')->nullable();
            $table->string('identifier')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('brand')->nullable();
            $table->longText('description')->nullable();
            $table->longText('private_comments')->nullable();
            $table->string('model')->nullable();
            $table->enum('subject_to_maintenance', ['0', '1'])->nullable();
            $table->date('start_of_warranty')->nullable();
            $table->date('end_of_warranty')->nullable();
            $table->string('main_image')->nullable();
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
        Schema::dropIfExists('client_assets');
    }
}

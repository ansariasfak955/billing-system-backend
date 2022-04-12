<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTechnicalIncidentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('technical_incidents', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable();
            $table->string('notifications')->nullable();
            $table->string('client_id')->nullable();
            $table->string('address')->nullable();
            $table->string('priority')->nullable();
            $table->string('status')->nullable();
            $table->string('status_changed_by')->nullable();
            $table->longText('description')->nullable();
            $table->string('created_by')->nullable();
            $table->dateTime('date')->nullable();
            $table->dateTime('assigned_date')->nullable();
            $table->string('invoice_to')->nullable();
            $table->dateTime('closing_date')->nullable();
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
        Schema::dropIfExists('technical_incidents');
    }
}

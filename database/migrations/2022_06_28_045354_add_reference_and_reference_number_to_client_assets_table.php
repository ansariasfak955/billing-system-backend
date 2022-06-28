<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferenceAndReferenceNumberToClientAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_assets', function (Blueprint $table) {
            $table->string('reference')->after('id')->nullable();
            $table->string('reference_number')->after('reference')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('client_assets', function (Blueprint $table) {
            //
        });
    }
}

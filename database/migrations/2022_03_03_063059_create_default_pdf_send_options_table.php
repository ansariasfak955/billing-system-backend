<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDefaultPdfSendOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('default_pdf_send_options', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('format');
            $table->enum('price_after_tax', ['0', '1'])->default('0');
            $table->enum('mailing_format', ['0', '1'])->default('0');
            $table->enum('include_main_image', ['0', '1'])->default('0');
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
        Schema::dropIfExists('default_pdf_send_options');
    }
}
